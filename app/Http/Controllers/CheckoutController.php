<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Helpers\SafaricomDarajaHelper;
use App\Models\Setting;
use App\Mail\OrderPlacedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::details();
        if (empty($cart['lines'])) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        $settings = Setting::getCached();
        $enableMpesa = $settings?->enable_mpesa ?? true;
        $enableCod = $settings?->enable_cod ?? true;
        $availablePaymentMethods = [
            'mpesa' => (bool) $enableMpesa,
            'cod' => (bool) $enableCod,
        ];
        $addresses = collect();
        $counties = collect();
        if (auth()->check()) {
            $addresses = \App\Models\Address::where('user_id', auth()->id())
                ->orderByDesc('is_default')
                ->orderByDesc('id')
                ->get();
        }
        if (Schema::hasTable('counties')) {
            $counties = \App\Models\County::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->pluck('name');
        }

        return view('theme::checkout.index', compact('cart','addresses','availablePaymentMethods', 'counties'));
    }

    public function process(Request $request): RedirectResponse
    {
        $cart = Cart::details();
        if (empty($cart['lines'])) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $settings = Setting::getCached();
        $enabled = [];
        if ($settings?->enable_mpesa ?? true) {
            $enabled[] = 'mpesa';
        }
        if ($settings?->enable_cod ?? true) {
            $enabled[] = 'cod';
        }
        if (empty($enabled)) {
            $enabled = ['mpesa', 'cod'];
        }

        $rules = [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'payment_method' => ['required', Rule::in($enabled)],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'create_account' => ['sometimes', 'boolean'],
            'save_address' => ['sometimes', 'boolean'],
            'billing_same' => ['sometimes', 'boolean'],
        ];
        if (!$request->boolean('billing_same', true)) {
            $rules = array_merge($rules, [
                'billing_full_name' => ['required', 'string', 'max:255'],
                'billing_email' => ['required', 'email'],
                'billing_address' => ['required', 'string', 'max:255'],
                'billing_city' => ['nullable', 'string', 'max:255'],
                'billing_state' => ['nullable', 'string', 'max:255'],
                'billing_postal_code' => ['nullable', 'string', 'max:20'],
            ]);
        }
        if (!Auth::check() && $request->boolean('create_account')) {
            $rules = array_merge($rules, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
        }
        $data = $request->validate($rules);

        if (!Auth::check() && $request->boolean('create_account')) {
            $user = User::create([
                'name' => (string) $request->input('name'),
                'email' => strtolower((string) $request->input('email')),
                'password' => Hash::make((string) $request->input('password')),
                'user_type' => 'buyer',
                'address' => (string) $request->input('address'),
                'city' => (string) $request->input('city'),
                'state' => (string) $request->input('state'),
                'postal_code' => (string) $request->input('postal_code'),
            ]);
            Auth::login($user);
        }

        $order = DB::transaction(function () use ($request, $data, $cart) {
            // Compute billing vs shipping
            $billing = [
                'billing_full_name' => $request->boolean('billing_same', true) ? $data['full_name'] : ($data['billing_full_name'] ?? null),
                'billing_email' => $request->boolean('billing_same', true) ? $data['email'] : ($data['billing_email'] ?? null),
                'billing_address' => $request->boolean('billing_same', true) ? $data['address'] : ($data['billing_address'] ?? null),
                'billing_city' => $request->boolean('billing_same', true) ? ($data['city'] ?? null) : ($data['billing_city'] ?? null),
                'billing_state' => $request->boolean('billing_same', true) ? ($data['state'] ?? null) : ($data['billing_state'] ?? null),
                'billing_postal_code' => $request->boolean('billing_same', true) ? ($data['postal_code'] ?? null) : ($data['billing_postal_code'] ?? null),
            ];
            $order = Order::create([
                'user_id' => auth()->id(),
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'],
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                ...$billing,
                'total' => $cart['total'],
                'status' => 'pending',
            ]);

            foreach ($cart['lines'] as $line) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $line['product']->id,
                    'quantity' => $line['quantity'],
                    'price' => $line['price'],
                    'line_total' => $line['line_total'],
                ]);
            }

            // Store applied coupon on order, if any
            $coupon = \App\Support\Cart::coupon();
            if($coupon){
                $order->update([
                    'coupon_code' => $coupon['code'] ?? null,
                    'coupon_discount' => (float) ($cart['discount'] ?? 0),
                    'promotion_id' => $coupon['promotion_id'] ?? null,
                ]);
            }

            if ($request->input('payment_method') === 'mpesa') {
                // Initiate MPESA STK push
                $reference = 'ORDER-' . $order->id;
                $description = 'Order #' . $order->id . ' payment';
                $phone = preg_replace('/\s+/', '', (string) $request->input('phone'));
                if (str_starts_with($phone, '0')) {
                    $phone = '254' . substr($phone, 1);
                }
                $resp = SafaricomDarajaHelper::stkPushRequest($phone, $cart['total'], $reference, $description);
                if (($resp['status'] ?? '') === 'success') {
                    $dataResp = $resp['data'] ?? [];
                    $order->update([
                        'payment_method' => 'mpesa',
                        'mpesa_merchant_request_id' => $dataResp['MerchantRequestID'] ?? null,
                        'mpesa_checkout_request_id' => $dataResp['CheckoutRequestID'] ?? null,
                        'mpesa_phone' => $phone,
                        'mpesa_amount' => $cart['total'],
                        'status' => 'pending',
                    ]);
                }
            } else {
                // Cash on delivery
                $order->update([
                    'payment_method' => 'cod',
                    'status' => 'awaiting_delivery',
                ]);
            }

            // Email customer
            try {
                Mail::to($order->email)->send(new OrderPlacedMail($order));
            } catch (\Throwable $e) {
                // Fail silently; logging could be added here
            }

            return $order;
        });

        // Save address to profile if requested
        if (Auth::check() && $request->boolean('save_address')) {
            $u = $request->user();
            $u->fill([
                'address' => $data['address'],
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
            ])->save();
        }

        Cart::clear();

        $msg = $request->input('payment_method') === 'mpesa'
            ? 'Order placed! Approve the STK popup on your phone to complete payment.'
            : 'Order placed! You chose Pay on Pickup. We will contact you shortly.';
        return redirect()->route('orders.success', $order)->with('success', $msg);
    }

    public function success(Order $order)
    {
        return view('theme::orders.success', compact('order'));
    }
}
