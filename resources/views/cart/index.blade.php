@extends('theme::layouts.app')

@section('content')
<x-breadcrumbs :items="[
  ['label' => 'Home', 'url' => route('products.index')],
  ['label' => 'Cart']
]" />
<h1 class="text-2xl font-semibold mb-4">Your Cart</h1>

@if(empty($cart['lines']))
    <p>Your cart is empty.</p>
@else
    <form action="{{ route('cart.update') }}" method="POST" class="bg-white border rounded p-3">
        @csrf
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-3 border">Product</th>
                    <th class="p-3 border">Price</th>
                    <th class="p-3 border">Qty</th>
                    <th class="p-3 border">Total</th>
                    <th class="p-3 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart['lines'] as $i => $line)
                <tr>
                    <td class="p-3 border">
                        <a href="{{ route('products.show', $line['product']) }}" class="hover:underline">{{ $line['product']->name }}</a>
                    </td>
                    <td class="p-3 border">{{ currency_format($line['price']) }}</td>
                    <td class="p-3 border">
                        <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $line['product']->id }}">
                        <div class="inline-flex items-center border rounded">
                            <button type="button" class="px-2" onclick="var el=this.nextElementSibling; el.stepDown(); el.dispatchEvent(new Event('change'));">−</button>
                            <input class="px-2 py-1 w-16 text-center" type="number" name="items[{{ $i }}][quantity]" min="0" value="{{ $line['quantity'] }}" onchange="this.form.submit()">
                            <button type="button" class="px-2" onclick="var el=this.previousElementSibling; el.stepUp(); el.dispatchEvent(new Event('change'));">+</button>
                        </div>
                    </td>
                    <td class="p-3 border">{{ currency_format($line['line_total']) }}</td>
                    <td class="p-3 border">
                        <form action="{{ route('cart.remove', $line['product']) }}" method="POST" onsubmit="return confirm('Remove this item?')">
                            @csrf
                            <button class="px-2 py-1 text-sm bg-red-600 text-white rounded">Remove</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 flex items-center justify-between">
            <div>
                <button class="px-3 py-2 bg-gray-800 text-white rounded hover:bg-black">Update Cart</button>
                <form class="inline" action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Clear cart?')">
                    @csrf
                    <button class="ml-2 px-3 py-2 bg-gray-200 rounded">Clear</button>
                </form>
            </div>
            <div class="text-right">
                <div>Subtotal: <strong>{{ currency_format($cart['subtotal']) }}</strong></div>
                @if(($cart['discount'] ?? 0) > 0)
                    <div class="text-green-700">Discount @if($cart['coupon']) ({{ $cart['coupon']['code'] }}) @endif: <strong>− {{ currency_format($cart['discount']) }}</strong></div>
                @endif
                @if(($cart['shipping'] ?? 0) > 0)
                    <div>Shipping: <strong>{{ currency_format($cart['shipping']) }}</strong></div>
                @endif
                @if(($cart['tax'] ?? 0) > 0)
                    <div>Tax: <strong>{{ currency_format($cart['tax']) }}</strong></div>
                @endif
                <div class="mt-1">Total: <strong>{{ currency_format($cart['total']) }}</strong></div>
                <a href="{{ route('checkout.index') }}" class="mt-3 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Proceed to Checkout</a>
                <div class="mt-3">
                    @if(!$cart['coupon'])
                    <form action="{{ route('cart.coupon') }}" method="POST" class="inline-flex gap-2 items-center">
                        @csrf
                        <input name="code" class="border rounded px-3 py-2" placeholder="Promo code" />
                        <button class="px-3 py-2 bg-gray-200 rounded">Apply</button>
                    </form>
                    @else
                    <form action="{{ route('cart.coupon.remove') }}" method="POST" class="inline">
                        @csrf
                        <button class="text-sm text-red-700 hover:underline">Remove promo ({{ $cart['coupon']['code'] }})</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </form>
@endif
@endsection


