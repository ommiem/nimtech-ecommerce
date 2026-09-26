@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-semibold mb-4">My Orders</h1>

@if($orders->count() === 0)
  <div class="bg-white border rounded p-6 text-gray-600">You don't have any orders yet.</div>
@else
<div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
  <div class="flex flex-wrap items-center gap-2 text-sm">
    <a href="{{ route('account.orders.index') }}" class="px-2 py-1 border rounded {{ request('status') ? '' : 'bg-gray-200' }}">All</a>
    @foreach(['pending' => 'Pending', 'awaiting_delivery'=>'Awaiting Delivery', 'paid' => 'Paid', 'failed' => 'Failed'] as $key=>$label)
      <a href="{{ route('account.orders.index', ['status' => $key]) }}" class="px-2 py-1 border rounded {{ request('status')===$key ? 'bg-gray-200' : '' }}">{{ $label }}</a>
    @endforeach
  </div>
  <form method="GET" class="text-sm w-full sm:w-auto flex gap-2">
    <input type="hidden" name="status" value="{{ request('status') }}">
    <input class="border rounded px-2 py-1 w-full sm:w-64" type="text" name="q" value="{{ request('q') }}" placeholder="Search by #ID">
    <button class="px-2 py-1 border rounded flex-shrink-0">Search</button>
  </form>
</div>

<!-- Desktop/tablet table -->
<div class="bg-white border rounded hidden md:block">
    <table class="min-w-full">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="p-3 border">Order #</th>
          <th class="p-3 border">Date</th>
          <th class="p-3 border">Status</th>
          <th class="p-3 border">Total</th>
          <th class="p-3 border">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($orders as $o)
        <tr>
          <td class="p-3 border">#{{ $o->id }}</td>
          <td class="p-3 border">{{ $o->created_at->format('M j, Y g:i A') }}</td>
          <td class="p-3 border">
            @php($st = strtolower($o->status))
            @php($cls = match($st){
              'paid' => 'bg-green-100 text-green-800',
              'pending' => 'bg-yellow-100 text-yellow-800',
              'failed' => 'bg-red-100 text-red-800',
              'awaiting_delivery' => 'bg-indigo-100 text-indigo-800',
              default => 'bg-gray-100 text-gray-800'
            })
            <span class="px-2 py-1 rounded text-xs font-semibold {{ $cls }}">{{ ucfirst($o->status) }}</span>
          </td>
          <td class="p-3 border">{{ currency_format($o->total) }}</td>
          <td class="p-3 border space-x-2">
            <a class="text-blue-700 hover:underline" href="{{ route('account.orders.show', $o) }}">View</a>
            @if(strtolower($o->status) !== 'paid')
              @php($retryPhone = $o->mpesa_phone ?? $o->phone)
              @if(($o->payment_method ?? 'mpesa') === 'cod')
                <form class="inline" method="POST" action="{{ route('account.orders.paynow', $o) }}">
                  @csrf
                  <input type="hidden" name="phone" value="{{ $retryPhone }}">
                  <button class="text-blue-700 hover:underline" title="Pay now with M‑Pesa">Pay Now</button>
                </form>
              @else
                @if($retryPhone)
                  <form class="inline" method="POST" action="{{ route('account.orders.retry', $o) }}">
                    @csrf
                    <input type="hidden" name="phone" value="{{ $retryPhone }}">
                    <button class="text-blue-700 hover:underline" title="Retry via STK">Retry</button>
                  </form>
                @else
                  <a class="text-blue-700 hover:underline" href="{{ route('account.orders.show', $o) }}" title="Retry payment">Retry</a>
                @endif
              @endif
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <!-- Mobile list cards -->
  <div class="md:hidden bg-white border rounded divide-y">
    @foreach($orders as $o)
      <div class="p-3">
        <div class="flex justify-between items-center">
          <div class="font-medium">#{{ $o->id }}</div>
          <div class="text-sm text-gray-600">{{ $o->created_at->format('M j, Y g:i A') }}</div>
        </div>
        <div class="mt-2 flex items-center gap-2">
          @php($st = strtolower($o->status))
          @php($cls = match($st){
            'paid' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            'awaiting_delivery' => 'bg-indigo-100 text-indigo-800',
            default => 'bg-gray-100 text-gray-800'
          })
          <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $cls }}">{{ ucfirst($o->status) }}</span>
          <span class="ml-auto font-medium">{{ currency_format($o->total) }}</span>
        </div>
        <div class="mt-2 flex items-center gap-3">
          <a class="text-blue-700 hover:underline" href="{{ route('account.orders.show', $o) }}">View</a>
          @if(strtolower($o->status) !== 'paid')
            @php($retryPhone = $o->mpesa_phone ?? $o->phone)
            @if(($o->payment_method ?? 'mpesa') === 'cod')
              <form class="inline" method="POST" action="{{ route('account.orders.paynow', $o) }}">
                @csrf
                <input type="hidden" name="phone" value="{{ $retryPhone }}">
                <button class="text-blue-700 hover:underline" title="Pay now with M-Pesa">Pay Now</button>
              </form>
            @else
              @if($retryPhone)
                <form class="inline" method="POST" action="{{ route('account.orders.retry', $o) }}">
                  @csrf
                  <input type="hidden" name="phone" value="{{ $retryPhone }}">
                  <button class="text-blue-700 hover:underline" title="Retry via STK">Retry</button>
                </form>
              @else
                <a class="text-blue-700 hover:underline" href="{{ route('account.orders.show', $o) }}" title="Retry payment">Retry</a>
              @endif
            @endif
          @endif
        </div>
      </div>
    @endforeach
  </div>
  <div class="mt-4">{{ $orders->links() }}</div>
@endif
@endsection
