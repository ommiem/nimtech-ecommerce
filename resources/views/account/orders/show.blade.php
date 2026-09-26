@extends('layouts.app')

@section('content')
<a class="text-sm text-gray-600 hover:underline" href="{{ route('account.orders.index') }}">&larr; Back to orders</a>
<h1 class="text-2xl font-semibold mt-2">Order #{{ $order->id }}</h1>
<div class="text-gray-600">Placed {{ $order->created_at->format('M j, Y g:i A') }}</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
  <div class="lg:col-span-2 bg-white border rounded p-4">
    <h2 class="text-lg font-semibold mb-3">Items</h2>
    <table class="min-w-full hidden md:table">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="p-3 border">Product</th>
          <th class="p-3 border">Price</th>
          <th class="p-3 border">Qty</th>
          <th class="p-3 border">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $item)
        <tr>
          <td class="p-3 border">{{ $item->product?->name ?? 'Product #'.$item->product_id }}</td>
          <td class="p-3 border">{{ currency_format($item->price) }}</td>
          <td class="p-3 border">{{ $item->quantity }}</td>
          <td class="p-3 border">{{ currency_format($item->line_total) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="md:hidden divide-y">
      @foreach($order->items as $item)
        <div class="py-3">
          <div class="font-medium">{{ $item->product?->name ?? ('Product #'.$item->product_id) }}</div>
          <div class="mt-1 text-sm text-gray-700 flex items-center gap-3">
            <span>{{ currency_format($item->price) }}</span>
            <span>Qty: {{ $item->quantity }}</span>
            <span class="ml-auto font-semibold">{{ currency_format($item->line_total) }}</span>
          </div>
        </div>
      @endforeach
    </div>
  </div>
  <div class="bg-white border rounded p-4">
    <h2 class="text-lg font-semibold mb-3">Summary</h2>
    <div class="space-y-1 text-sm">
      <div class="flex justify-between"><span>Status</span><span class="font-semibold">{{ ucfirst($order->status) }}</span></div>
      <div class="flex justify-between"><span>Total</span><span class="font-semibold">{{ currency_format($order->total) }}</span></div>
      @if($order->mpesa_receipt)
      <div class="flex justify-between"><span>Receipt</span><span class="font-semibold">{{ $order->mpesa_receipt }}</span></div>
      @endif
      @if($order->mpesa_phone)
      <div class="flex justify-between"><span>Paid Phone</span><span class="font-semibold">{{ $order->mpesa_phone }}</span></div>
      @endif
      @if($order->mpesa_amount)
      <div class="flex justify-between"><span>Paid Amount</span><span class="font-semibold">{{ currency_format($order->mpesa_amount) }}</span></div>
      @endif
    </div>
    <div class="mt-4 text-sm text-gray-600">
      <div class="font-semibold mb-1">Shipping Address</div>
      <div>{{ $order->full_name }}</div>
      <div>{{ $order->address }}</div>
      <div>Town/Area: {{ $order->city ?: '-' }}</div>
      <div>County: {{ $order->state ?: '-' }}</div>
      <div>Postal Code / P.O. Box: {{ $order->postal_code ?: '-' }}</div>
      <div class="mt-1">Email: {{ $order->email }}</div>
    </div>
    <div class="mt-4 text-sm text-gray-600">
      <div class="font-semibold mb-1">Billing Address</div>
      <div>{{ $order->billing_full_name ?? $order->full_name }}</div>
      <div>{{ $order->billing_address ?? $order->address }}</div>
      <div>Town/Area: {{ ($order->billing_city ?? $order->city) ?: '-' }}</div>
      <div>County: {{ ($order->billing_state ?? $order->state) ?: '-' }}</div>
      <div>Postal Code / P.O. Box: {{ ($order->billing_postal_code ?? $order->postal_code) ?: '-' }}</div>
      <div class="mt-1">Email: {{ $order->billing_email ?? $order->email }}</div>
    </div>
  </div>

  @php($settings = \App\Models\Setting::getCached())
  @if($order->status !== 'paid' && ($order->payment_method ?? 'mpesa') !== 'cod' && ($settings?->enable_mpesa ?? true))
  <div class="bg-white border rounded p-4">
    <h2 class="text-lg font-semibold mb-3">Retry Payment</h2>
    <form action="{{ route('account.orders.retry', $order) }}" method="POST" class="space-y-3">
      @csrf
      <div>
        <label class="block text-sm">Phone (for STK)</label>
        <input class="w-full border rounded px-3 py-2" type="tel" name="phone" value="{{ old('phone', $order->mpesa_phone ?? $order->phone) }}" required>
        @error('phone')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Send STK Push</button>
    </form>
  </div>
  @endif

  @if($order->status !== 'paid' && ($order->payment_method ?? 'mpesa') === 'cod' && ($settings?->enable_mpesa ?? true))
  <div class="bg-white border rounded p-4">
    <h2 class="text-lg font-semibold mb-3">Pay Now with M-Pesa</h2>
    <p class="text-sm text-gray-600 mb-2">Switch this COD order to M-Pesa payment by sending an STK push to your phone.</p>
    <form action="{{ route('account.orders.paynow', $order) }}" method="POST" class="space-y-3">
      @csrf
      <div>
        <label class="block text-sm">Phone (for STK)</label>
        <input class="w-full border rounded px-3 py-2" type="tel" name="phone" value="{{ old('phone', $order->mpesa_phone ?? $order->phone) }}" required>
        @error('phone')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Send STK Push</button>
    </form>
  </div>
  @endif
</div>
</div>
@endsection
