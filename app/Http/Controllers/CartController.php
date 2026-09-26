<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::details();
        return view('theme::cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);
        $product = Product::findOrFail($data['product_id']);
        $requested = (int) ($data['quantity'] ?? 1);
        $requested = max(1, $requested);

        // Respect available stock if set
        $existing = Cart::all()[$product->id] ?? 0;
        if (is_numeric($product->stock)) {
            $availableToAdd = max(0, (int) $product->stock - (int) $existing);
            if ($availableToAdd <= 0) {
                return back()->with('error', 'Not enough stock available.');
            }
            $requested = min($requested, $availableToAdd);
        }

        Cart::add($product->id, $requested);

        // Optional redirect to checkout (Buy Now)
        if ($request->query('redirect') === 'checkout') {
            return redirect()->route('checkout.index');
        }
        return back()->with('success', 'Added to cart.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($data['items'] as $item) {
            Cart::update((int) $item['product_id'], (int) $item['quantity']);
        }
        return back()->with('success', 'Cart updated.');
    }

    public function remove(Product $product)
    {
        Cart::remove($product->id);
        return back()->with('success', 'Item removed.');
    }

    public function clear()
    {
        Cart::clear();
        return back()->with('success', 'Cart cleared.');
    }

    public function applyCoupon(Request $request)
    {
        $data = $request->validate([
            'code' => ['required','string','max:50']
        ]);
        $coupon = Cart::applyCoupon($data['code']);
        if(!$coupon){
            return back()->with('error', 'Invalid promo code.')->withInput();
        }
        return back()->with('success', 'Promo applied: '.$coupon['label']);
    }

    public function removeCoupon()
    {
        Cart::removeCoupon();
        return back()->with('success', 'Promo removed.');
    }
}
