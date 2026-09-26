<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SafaricomDarajaHelper;
use App\Models\Setting;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $status = $request->query('status');
        $q = $request->query('q');

        $query = Order::query()
            ->where(function ($qbuilder) use ($user) {
                $qbuilder->where('user_id', $user->id)
                  ->orWhere(function ($q2) use ($user) {
                      $q2->whereNull('user_id')->where('email', $user->email);
                  });
            });
        if ($status) {
            $query->where('status', $status);
        }
        if ($q) {
            $query->where('id', (int) $q);
        }
        $orders = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('account.orders.index', compact('orders','status','q'));
    }

    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        $owns = ($order->user_id && $order->user_id === $user->id)
            || (!$order->user_id && $order->email === $user->email);

        abort_unless($owns, 403);

        $order->load('items.product');

        return view('account.orders.show', compact('order'));
    }

    public function retryPayment(Request $request, Order $order)
    {
        $user = $request->user();
        $owns = ($order->user_id && $order->user_id === $user->id)
            || (!$order->user_id && $order->email === $user->email);
        abort_unless($owns, 403);

        if ($order->status === 'paid') {
            return back()->with('success', 'Order already paid.');
        }
        $settings = Setting::getCached();
        if (!($settings?->enable_mpesa ?? true)) {
            return back()->with('error', 'M-Pesa payments are currently disabled.');
        }

        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $phone = preg_replace('/\s+/', '', (string) $data['phone']);
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }

        $resp = SafaricomDarajaHelper::stkPushRequest($phone, $order->total, 'ORDER-'.$order->id, 'Order #'.$order->id.' payment retry');
        if (($resp['status'] ?? '') === 'success') {
            $dataResp = $resp['data'] ?? [];
            $order->update([
                'payment_method' => 'mpesa',
                'mpesa_merchant_request_id' => $dataResp['MerchantRequestID'] ?? null,
                'mpesa_checkout_request_id' => $dataResp['CheckoutRequestID'] ?? null,
                'mpesa_phone' => $phone,
                'mpesa_amount' => $order->total,
                'status' => 'pending',
            ]);
            return back()->with('success', 'STK push sent. Check your phone to approve payment.');
        }

        return back()->with('error', $resp['message'] ?? 'Failed to initiate payment.');
    }

    public function payNow(Request $request, Order $order)
    {
        $user = $request->user();
        $owns = ($order->user_id && $order->user_id === $user->id)
            || (!$order->user_id && $order->email === $user->email);
        abort_unless($owns, 403);

        if ($order->status === 'paid') {
            return back()->with('success', 'Order already paid.');
        }
        $settings = Setting::getCached();
        if (!($settings?->enable_mpesa ?? true)) {
            return back()->with('error', 'M-Pesa payments are currently disabled.');
        }

        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $phone = preg_replace('/\s+/', '', (string) $data['phone']);
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }

        $resp = SafaricomDarajaHelper::stkPushRequest($phone, $order->total, 'ORDER-'.$order->id, 'Order #'.$order->id.' payment');
        if (($resp['status'] ?? '') === 'success') {
            $dataResp = $resp['data'] ?? [];
            $order->update([
                'payment_method' => 'mpesa',
                'mpesa_merchant_request_id' => $dataResp['MerchantRequestID'] ?? null,
                'mpesa_checkout_request_id' => $dataResp['CheckoutRequestID'] ?? null,
                'mpesa_phone' => $phone,
                'mpesa_amount' => $order->total,
                'status' => 'pending',
            ]);
            return back()->with('success', 'STK push sent. Check your phone to approve payment.');
        }

        return back()->with('error', $resp['message'] ?? 'Failed to initiate payment.');
    }
}
