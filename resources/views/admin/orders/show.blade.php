@extends('layouts.admin')

@section('content')
<div class="mb-4">
  <a class="text-sm text-gray-600 hover:underline" href="{{ route('admin.orders.index') }}">&larr; Back to orders</a>
  <div class="flex items-start justify-between mt-2 gap-4">
    <div>
      <h1 class="text-2xl font-semibold">Order #{{ $order->id }}</h1>
      <div class="text-gray-600 text-sm">Placed {{ $order->created_at?->format('M j, Y g:i A') }}</div>
    </div>
    <div class="text-right">
      <div class="text-sm text-gray-600">Status</div>
      <div class="font-semibold inline-flex px-2 py-1 rounded text-xs {{ match($order->status) {
        'paid' => 'bg-green-100 text-green-800',
        'pending' => 'bg-yellow-100 text-yellow-800',
        'awaiting_delivery' => 'bg-indigo-100 text-indigo-800',
        'processing' => 'bg-blue-100 text-blue-800',
        'shipped' => 'bg-purple-100 text-purple-800',
        'delivered' => 'bg-green-50 text-green-700',
        'failed' => 'bg-red-100 text-red-800',
        'cancelled' => 'bg-gray-100 text-gray-800',
        default => 'bg-gray-100 text-gray-800',
      } }}">
        {{ ucfirst(str_replace('_',' ', $order->status ?? '')) }}
      </div>
    </div>
  </div>
</div>

@if(session('success'))
  <div class="mb-4 p-3 bg-green-100 text-green-800 border border-green-200 rounded">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="mb-4 p-3 bg-red-100 text-red-800 border border-red-200 rounded">{{ session('error') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2 bg-white border rounded p-4">
    <h2 class="text-lg font-semibold mb-3">Items</h2>
    <table class="min-w-full">
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
          <td class="p-3 border">{{ $item->product?->name ?? ('Product #'.$item->product_id) }}</td>
          <td class="p-3 border">{{ currency_format($item->price) }}</td>
          <td class="p-3 border">{{ $item->quantity }}</td>
          <td class="p-3 border">{{ currency_format($item->line_total) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="bg-white border rounded p-4 space-y-6">
    <div>
      <h2 class="text-lg font-semibold mb-3">Summary</h2>
      <div class="space-y-1 text-sm">
        <div class="flex justify-between"><span>Total</span><span class="font-semibold">{{ currency_format($order->total) }}</span></div>
        @if(($order->coupon_discount ?? 0) > 0)
          <div class="flex justify-between text-green-700"><span>Discount @if($order->coupon_code) ({{ $order->coupon_code }}) @endif</span><span class="font-semibold">-{{ currency_format($order->coupon_discount) }}</span></div>
        @endif
        <div class="flex justify-between"><span>Payment</span><span class="font-semibold">{{ strtoupper($order->payment_method ?? '-') }}</span></div>
        <div class="flex justify-between"><span>Phone</span><span class="font-semibold">{{ $order->mpesa_phone ?? $order->phone ?? '-' }}</span></div>
        <div class="flex justify-between"><span>Paid At</span><span class="font-semibold">{{ $order->paid_at?->format('M j, Y g:i A') ?? '-' }}</span></div>
        <div class="flex justify-between"><span>Receipt</span><span class="font-semibold">{{ $order->mpesa_receipt ?? '-' }}</span></div>
      </div>
    </div>

    <div class="text-sm text-gray-600">
      <div class="font-semibold mb-1 text-gray-800">Customer</div>
      <div>{{ $order->full_name }}</div>
      <div>{{ $order->email }}</div>
      @if($order->user)
        <div class="mt-1">User ID: {{ $order->user->id }}</div>
      @endif
    </div>

    <div class="text-sm text-gray-600">
      <div class="font-semibold mb-1 text-gray-800">Shipping Address</div>
      <div>{{ $order->address }}</div>
      <div>Town/Area: {{ $order->city ?: '-' }}</div>
      <div>County: {{ $order->state ?: '-' }}</div>
      <div>Postal Code / P.O. Box: {{ $order->postal_code ?: '-' }}</div>
    </div>

    <div class="text-sm text-gray-600">
      <div class="font-semibold mb-1 text-gray-800">Billing Address</div>
      <div>{{ $order->billing_full_name ?? $order->full_name }}</div>
      <div>{{ $order->billing_address ?? $order->address }}</div>
      <div>Town/Area: {{ ($order->billing_city ?? $order->city) ?: '-' }}</div>
      <div>County: {{ ($order->billing_state ?? $order->state) ?: '-' }}</div>
      <div>Postal Code / P.O. Box: {{ ($order->billing_postal_code ?? $order->postal_code) ?: '-' }}</div>
      <div class="mt-1">Email: {{ $order->billing_email ?? $order->email }}</div>
    </div>
  </div>
</div>

<div class="bg-white border rounded p-4 mt-6" x-data="{ open:false, status:'{{ $order->status }}' }">
  <div class="flex items-center justify-between mb-3">
    <h2 class="text-lg font-semibold">Admin Actions</h2>
    <button type="button" class="px-3 py-2 bg-blue-600 text-white rounded text-sm" @click="open=true">Change Status</button>
  </div>
  <p class="text-sm text-gray-600 mb-2">Current status: {{ ucfirst(str_replace('_',' ', $order->status)) }}</p>
  @if($order->status === 'paid')
    <p class="text-sm text-green-700">Order is paid; status changes are limited.</p>
  @elseif($order->status === 'cancelled')
    <p class="text-sm text-gray-600">Order is cancelled.</p>
  @endif

  <div x-show="open" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center" @keydown.escape.window="open=false">
    <div class="absolute inset-0 bg-black/30" @click="open=false"></div>
    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="relative bg-white border rounded shadow-lg p-5 w-full max-w-md space-y-3">
      @csrf
      <div class="flex items-start justify-between">
        <div>
          <h3 class="text-lg font-semibold">Change Status</h3>
          <p class="text-sm text-gray-600">Order #{{ $order->id }}</p>
        </div>
        <button type="button" class="text-gray-500" @click="open=false" aria-label="Close">✕</button>
      </div>

      <div>
        <label class="block text-sm font-medium">Status</label>
        <select name="status" x-model="status" class="w-full border rounded px-3 py-2 text-sm">
          <option value="pending" @selected($order->status==='pending')>Pending</option>
          <option value="awaiting_delivery" @selected($order->status==='awaiting_delivery')>Awaiting Delivery</option>
          <option value="processing" @selected($order->status==='processing')>Processing</option>
          <option value="shipped" @selected($order->status==='shipped')>Shipped</option>
          <option value="delivered" @selected($order->status==='delivered')>Delivered</option>
          <option value="paid" @selected($order->status==='paid') @if($order->status==='paid') disabled @endif>Paid</option>
          <option value="failed" @selected($order->status==='failed')>Failed</option>
          <option value="cancelled" @selected($order->status==='cancelled')>Cancelled</option>
        </select>
      </div>

      <div x-show="status === 'paid'" class="space-y-2" x-cloak>
        <div>
          <label class="block text-sm font-medium">Receipt</label>
          <input class="w-full border rounded px-3 py-2 text-sm" type="text" name="receipt" placeholder="Receipt code" x-bind:required="status==='paid'">
        </div>
        <div>
          <label class="block text-sm font-medium">Amount (optional)</label>
          <input class="w-full border rounded px-3 py-2 text-sm" type="number" step="0.01" name="amount" placeholder="Amount" value="{{ $order->mpesa_amount ?? $order->total }}">
        </div>
        <div>
          <label class="block text-sm font-medium">Phone (optional)</label>
          <input class="w-full border rounded px-3 py-2 text-sm" type="text" name="phone" placeholder="Phone" value="{{ $order->mpesa_phone ?? $order->phone }}">
        </div>
      </div>

      <div class="flex justify-end gap-2 pt-2">
        <button type="button" class="px-3 py-2 bg-gray-200 rounded text-sm" @click="open=false">Close</button>
        <button class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Update</button>
      </div>

      @if($order->status === 'paid')
        <p class="text-xs text-gray-500">Paid orders cannot be moved to another status.</p>
      @elseif($order->status === 'cancelled')
        <p class="text-xs text-gray-500">Cancelled orders cannot be reopened.</p>
      @endif
    </form>
  </div>
</div>
@endsection
