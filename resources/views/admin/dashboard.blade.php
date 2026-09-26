@extends('layouts.admin')

@section('content')
<div class="space-y-6">
  <div class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <p class="text-sm opacity-80">Store health</p>
        <h1 class="text-3xl font-semibold">Admin Dashboard</h1>
      </div>
      <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded text-sm">View Orders</a>
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-white text-blue-700 rounded text-sm font-medium">Add Product</a>
        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded text-sm">View Store</a>
      </div>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mt-4">
      <div class="bg-white/10 rounded-lg p-3">
        <div class="text-xs opacity-80">Revenue (paid+in transit)</div>
        <div class="text-2xl font-semibold mt-1">{{ currency_format($stats['revenue'] ?? 0) }}</div>
        <div class="text-xs opacity-75">Avg order {{ currency_format($stats['avgOrder'] ?? 0) }}</div>
      </div>
      <div class="bg-white/10 rounded-lg p-3">
        <div class="text-xs opacity-80">Orders</div>
        <div class="text-2xl font-semibold mt-1">{{ $stats['totalOrders'] ?? 0 }}</div>
        <div class="text-xs opacity-75">{{ $stats['paidOrders'] ?? 0 }} paid / {{ $stats['inProgress'] ?? 0 }} active</div>
      </div>
      <div class="bg-white/10 rounded-lg p-3">
        <div class="text-xs opacity-80">Products</div>
        <div class="text-2xl font-semibold mt-1">{{ $stats['products'] ?? 0 }}</div>
        <div class="text-xs opacity-75">{{ $stats['lowStock'] ?? 0 }} low stock</div>
      </div>
      <div class="bg-white/10 rounded-lg p-3">
        <div class="text-xs opacity-80">Customers</div>
        <div class="text-2xl font-semibold mt-1">{{ $stats['buyers'] ?? 0 }}</div>
        <div class="text-xs opacity-75">{{ $stats['admins'] ?? 0 }} staff/admin</div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white border rounded-xl p-4 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-lg">Recent Orders</h2>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left text-gray-600">
              <th class="pb-2">Order</th>
              <th class="pb-2">Customer</th>
              <th class="pb-2">Status</th>
              <th class="pb-2">Total</th>
              <th class="pb-2">Placed</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse($recentOrders as $order)
            @php
              $badge = [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'awaiting_delivery' => 'bg-indigo-100 text-indigo-800',
                'processing' => 'bg-blue-100 text-blue-800',
                'shipped' => 'bg-purple-100 text-purple-800',
                'delivered' => 'bg-green-50 text-green-700',
                'paid' => 'bg-green-100 text-green-800',
                'failed' => 'bg-red-100 text-red-800',
                'cancelled' => 'bg-gray-100 text-gray-800',
              ][$order->status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <tr>
              <td class="py-2"><a href="{{ route('admin.orders.show', $order) }}" class="text-blue-700 hover:underline">#{{ $order->id }}</a></td>
              <td class="py-2">
                <div class="font-medium text-gray-800">{{ $order->full_name ?? $order->user?->name }}</div>
                <div class="text-xs text-gray-500">{{ $order->email }}</div>
              </td>
              <td class="py-2">
                <span class="px-2 py-1 rounded text-xs font-medium {{ $badge }}">{{ ucfirst(str_replace('_',' ', $order->status ?? '')) }}</span>
              </td>
              <td class="py-2 font-semibold">{{ currency_format($order->total) }}</td>
              <td class="py-2 text-gray-600 text-xs">{{ $order->created_at?->format('M j, Y g:i A') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="py-4 text-center text-gray-500">No orders yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="space-y-4">
      <div class="bg-white border rounded-xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
          <h2 class="font-semibold">Top Products</h2>
          <a href="{{ route('admin.products.index') }}" class="text-sm text-blue-600 hover:underline">Manage</a>
        </div>
        <div class="space-y-3">
          @forelse($topProducts as $item)
            @php
              $name = $item->product?->name ?? 'Product #'.$item->product_id;
              $maxQty = max(1, $topProducts->max('qty'));
              $bar = ($item->qty / $maxQty) * 100;
            @endphp
            <div>
              <div class="flex items-center justify-between text-sm font-medium text-gray-800">
                <span>{{ $name }}</span>
                <span>{{ $item->qty }} sold</span>
              </div>
              <div class="text-xs text-gray-500 mb-1">Revenue {{ currency_format($item->revenue ?? 0) }}</div>
              <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="h-2 rounded-full bg-blue-600" style="width: {{ $bar }}%"></div>
              </div>
            </div>
          @empty
            <p class="text-sm text-gray-500">No sales data yet.</p>
          @endforelse
        </div>
      </div>

      <div class="bg-white border rounded-xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
          <h2 class="font-semibold">New Customers</h2>
          <a href="{{ route('admin.buyers.index') }}" class="text-sm text-blue-600 hover:underline">View</a>
        </div>
        <div class="space-y-2">
          @forelse($recentCustomers as $cust)
            <div class="flex items-center justify-between text-sm">
              <div>
                <div class="font-medium text-gray-800">{{ $cust->name }}</div>
                <div class="text-xs text-gray-500">{{ $cust->email }}</div>
              </div>
              <div class="text-xs text-gray-500">{{ $cust->created_at?->diffForHumans() }}</div>
            </div>
          @empty
            <p class="text-sm text-gray-500">No customers yet.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <div class="bg-white border rounded-xl p-4 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold">Last 14 days</h2>
      <span class="text-xs text-gray-500">Orders & revenue snapshot</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <p class="text-sm font-medium text-gray-700 mb-2">Orders per day</p>
        <div class="space-y-2">
          @foreach($daily as $row)
            @php $pct = ($daily->max('orders') ?: 1); $width = ($row->orders / $pct) * 100; @endphp
            <div>
              <div class="flex items-center justify-between text-xs text-gray-600">
                <span>{{ \Carbon\Carbon::parse($row->d)->format('M j') }}</span>
                <span>{{ $row->orders }} orders</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $width }}%"></div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
      <div>
        <p class="text-sm font-medium text-gray-700 mb-2">Revenue per day</p>
        <div class="space-y-2">
          @foreach($daily as $row)
            @php $pct = ($daily->max('revenue') ?: 1); $width = ($row->revenue / $pct) * 100; @endphp
            <div>
              <div class="flex items-center justify-between text-xs text-gray-600">
                <span>{{ \Carbon\Carbon::parse($row->d)->format('M j') }}</span>
                <span>{{ currency_format($row->revenue ?? 0) }}</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="h-2 rounded-full bg-green-500" style="width: {{ $width }}%"></div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
