@extends('layouts.admin')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
  <h1 class="text-2xl font-semibold">Orders</h1>
  <div class="flex flex-col sm:flex-row sm:items-center gap-3">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex items-center gap-2">
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search by order #, name, phone" class="border rounded px-3 py-2 text-sm w-64">
      <input type="hidden" name="status" value="{{ request('status') }}">
      <button class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Search</button>
      @if(request('q'))
        <a href="{{ route('admin.orders.index', array_filter(['status'=>request('status')])) }}" class="text-sm text-gray-600">Clear</a>
      @endif
    </form>
    <div class="space-x-2 text-sm">
      @php $s = request('status'); @endphp
      @foreach([
        '' => 'All',
        'pending' => 'Pending',
        'awaiting_delivery' => 'Awaiting Delivery',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
      ] as $key => $label)
        <a href="{{ $key === '' ? route('admin.orders.index', array_filter(['q'=>request('q')])) : route('admin.orders.index', array_filter(['status' => $key, 'q'=>request('q')])) }}"
           class="px-2 py-1 border rounded {{ $s === $key || ($key === '' && !$s) ? 'bg-gray-200' : '' }}">{{ $label }}</a>
      @endforeach
    </div>
  </div>
</div>

@if(session('success'))
  <div class="mb-3 p-3 bg-green-100 text-green-800 border border-green-200 rounded">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="mb-3 p-3 bg-red-100 text-red-800 border border-red-200 rounded">{{ session('error') }}</div>
@endif

@php
  $statusClasses = [
    'pending' => 'bg-yellow-100 text-yellow-800',
    'awaiting_delivery' => 'bg-indigo-100 text-indigo-800',
    'processing' => 'bg-blue-100 text-blue-800',
    'shipped' => 'bg-purple-100 text-purple-800',
    'delivered' => 'bg-green-50 text-green-700',
    'paid' => 'bg-green-100 text-green-800',
    'failed' => 'bg-red-100 text-red-800',
    'cancelled' => 'bg-gray-100 text-gray-800',
  ];
@endphp

<div class="bg-white border rounded">
  <table class="min-w-full">
    <thead>
      <tr class="bg-gray-100 text-left">
        <th class="p-3 border">#</th>
        <th class="p-3 border">Customer</th>
        <th class="p-3 border">Status</th>
        <th class="p-3 border">Total</th>
        <th class="p-3 border">Paid At</th>
        <th class="p-3 border">Receipt</th>
        <th class="p-3 border">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($orders as $o)
      <tr>
        <td class="p-3 border"><a class="text-blue-700 hover:underline" href="{{ route('admin.orders.show', $o) }}">#{{ $o->id }}</a></td>
        <td class="p-3 border">
          {{ $o->full_name }}
          <div class="text-xs text-gray-500">{{ $o->email }}</div>
          <div class="text-xs text-gray-500">{{ $o->mpesa_phone ?? $o->phone ?? '-' }}</div>
        </td>
        <td class="p-3 border">
          <span class="inline-flex px-2 py-1 rounded text-xs font-medium {{ $statusClasses[$o->status] ?? 'bg-gray-100 text-gray-800' }}">
            {{ ucfirst(str_replace('_',' ', $o->status ?? '')) }}
          </span>
        </td>
        <td class="p-3 border">{{ currency_format($o->total) }}</td>
        <td class="p-3 border">{{ $o->paid_at?->format('M j, Y g:i A') ?? '-' }}</td>
        <td class="p-3 border">{{ $o->mpesa_receipt ?? '-' }}</td>
        <td class="p-3 border space-y-2" x-data="{ open:false, status:'{{ $o->status }}' }">
          <div class="flex items-center gap-2">
            <a class="inline-block text-sm text-blue-700 hover:underline" href="{{ route('admin.orders.show', $o) }}">View</a>
            <button type="button" class="px-2 py-1 bg-blue-600 text-white rounded text-xs" @click="open=true">Change Status</button>
          </div>
          @if($o->status === 'paid')
            <div class="text-xs text-green-700">Order is paid; changes are limited.</div>
          @elseif($o->status === 'cancelled')
            <div class="text-xs text-gray-600">Order is cancelled.</div>
          @endif

          <div x-show="open" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center" @keydown.escape.window="open=false">
            <div class="absolute inset-0 bg-black/30" @click="open=false"></div>
            <form method="POST" action="{{ route('admin.orders.update-status', $o) }}" class="relative bg-white border rounded shadow-lg p-4 w-full max-w-md space-y-3">
              @csrf
              <div class="flex items-start justify-between">
                <div>
                  <h3 class="text-lg font-semibold">Change Status</h3>
                  <p class="text-sm text-gray-600">Order #{{ $o->id }} — current: {{ ucfirst(str_replace('_',' ', $o->status)) }}</p>
                </div>
                <button type="button" class="text-gray-500" @click="open=false" aria-label="Close">✕</button>
              </div>
              <div>
                <label class="block text-sm font-medium">Status</label>
                <select name="status" x-model="status" class="w-full border rounded px-3 py-2 text-sm">
                  <option value="pending" @selected($o->status==='pending')>Pending</option>
                  <option value="awaiting_delivery" @selected($o->status==='awaiting_delivery')>Awaiting Delivery</option>
                  <option value="processing" @selected($o->status==='processing')>Processing</option>
                  <option value="shipped" @selected($o->status==='shipped')>Shipped</option>
                  <option value="delivered" @selected($o->status==='delivered')>Delivered</option>
                  <option value="paid" @selected($o->status==='paid') @if($o->status==='paid') disabled @endif>Paid</option>
                  <option value="failed" @selected($o->status==='failed')>Failed</option>
                  <option value="cancelled" @selected($o->status==='cancelled')>Cancelled</option>
                </select>
              </div>
              <div x-show="status === 'paid'" class="space-y-2" x-cloak>
                <div>
                  <label class="block text-sm font-medium">Receipt</label>
                  <input class="w-full border rounded px-3 py-2 text-sm" type="text" name="receipt" placeholder="Receipt code" x-bind:required="status==='paid'">
                </div>
                <div>
                  <label class="block text-sm font-medium">Amount (optional)</label>
                  <input class="w-full border rounded px-3 py-2 text-sm" type="number" step="0.01" name="amount" placeholder="Amount" value="{{ $o->mpesa_amount ?? $o->total }}">
                </div>
                <div>
                  <label class="block text-sm font-medium">Phone (optional)</label>
                  <input class="w-full border rounded px-3 py-2 text-sm" type="text" name="phone" placeholder="Phone" value="{{ $o->mpesa_phone ?? $o->phone }}">
                </div>
              </div>
              <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="px-3 py-2 bg-gray-200 rounded text-sm" @click="open=false">Close</button>
                <button class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Update</button>
              </div>
              @if($o->status === 'paid')
                <p class="text-xs text-gray-500">Paid orders cannot be moved to another status.</p>
              @elseif($o->status === 'cancelled')
                <p class="text-xs text-gray-500">Cancelled orders cannot be reopened.</p>
              @endif
            </form>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $orders->links() }}</div>
@endsection
