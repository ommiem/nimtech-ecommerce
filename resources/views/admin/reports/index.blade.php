@extends('layouts.admin')

@section('content')
<div class="space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <p class="text-sm text-gray-500">Insights</p>
      <h1 class="text-2xl font-semibold">Reports</h1>
      <p class="text-sm text-gray-500">From {{ $from->format('M j, Y') }} to {{ $to->format('M j, Y') }}</p>
    </div>
    <form method="GET" class="flex flex-wrap items-center gap-2">
      <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="border rounded px-3 py-2 text-sm">
      <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="border rounded px-3 py-2 text-sm">
      <button class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Apply</button>
    </form>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white border rounded-xl p-4 shadow-sm">
      <div class="text-xs text-gray-500">Revenue (paid+in transit)</div>
      <div class="text-2xl font-semibold mt-1">{{ currency_format($summary['revenue'] ?? 0) }}</div>
      <div class="text-xs text-gray-500">Avg order {{ currency_format($summary['avg'] ?? 0) }}</div>
    </div>
    <div class="bg-white border rounded-xl p-4 shadow-sm">
      <div class="text-xs text-gray-500">Orders</div>
      <div class="text-2xl font-semibold mt-1">{{ $summary['orders'] ?? 0 }}</div>
      <div class="text-xs text-gray-500">Across all statuses</div>
    </div>
    <div class="bg-white border rounded-xl p-4 shadow-sm">
      <div class="text-xs text-gray-500">Top Seller</div>
      <div class="text-lg font-semibold mt-1">{{ optional($topProducts->first()->product)->name ?? '—' }}</div>
      <div class="text-xs text-gray-500">Sold {{ $topProducts->first()->qty ?? 0 }} units</div>
    </div>
    <div class="bg-white border rounded-xl p-4 shadow-sm">
      <div class="text-xs text-gray-500">Best Customer</div>
      <div class="text-lg font-semibold mt-1">{{ $topCustomers->first()->full_name ?? '—' }}</div>
      <div class="text-xs text-gray-500">Spent {{ currency_format($topCustomers->first()->revenue ?? 0) }}</div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white border rounded-xl p-4 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-lg">Orders by status</h2>
      </div>
      <div class="space-y-3">
        @php $maxStatus = max(1, $byStatus->max('orders')); @endphp
        @forelse($byStatus as $row)
          @php $width = ($row->orders / $maxStatus) * 100; @endphp
          <div>
            <div class="flex items-center justify-between text-sm">
              <div class="font-medium text-gray-800">{{ ucfirst(str_replace('_',' ', $row->status ?? '')) }}</div>
              <div class="text-gray-600">{{ $row->orders }} orders</div>
            </div>
            <div class="text-xs text-gray-500 mb-1">Revenue {{ currency_format($row->revenue ?? 0) }}</div>
            <div class="w-full bg-gray-100 rounded-full h-2">
              <div class="h-2 rounded-full bg-blue-600" style="width: {{ $width }}%"></div>
            </div>
          </div>
        @empty
          <p class="text-sm text-gray-500">No orders in this range.</p>
        @endforelse
      </div>
    </div>

    <div class="bg-white border rounded-xl p-4 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-lg">Daily performance</h2>
      </div>
      <div class="space-y-3">
        @php $maxOrders = max(1, $daily->max('orders')); $maxRev = max(1, $daily->max('revenue')); @endphp
        @forelse($daily as $row)
          @php
            $ordersWidth = ($row->orders / $maxOrders) * 100;
            $revWidth = ($row->revenue / $maxRev) * 100;
          @endphp
          <div>
            <div class="flex items-center justify-between text-sm text-gray-700">
              <span>{{ \Carbon\Carbon::parse($row->d)->format('M j') }}</span>
              <span>{{ $row->orders }} orders / {{ currency_format($row->revenue ?? 0) }}</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2 mt-1">
              <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $ordersWidth }}%"></div>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2 mt-1">
              <div class="h-2 rounded-full bg-green-500" style="width: {{ $revWidth }}%"></div>
            </div>
          </div>
        @empty
          <p class="text-sm text-gray-500">No daily data in this range.</p>
        @endforelse
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white border rounded-xl p-4 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-lg">Top products</h2>
        <a href="{{ route('admin.products.index') }}" class="text-sm text-blue-600 hover:underline">Manage</a>
      </div>
      <div class="space-y-3">
        @php $maxQty = max(1, $topProducts->max('qty')); @endphp
        @forelse($topProducts as $item)
          @php $bar = ($item->qty / $maxQty) * 100; @endphp
          <div>
            <div class="flex items-center justify-between text-sm font-medium text-gray-800">
              <span>{{ $item->product?->name ?? 'Product #'.$item->product_id }}</span>
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
        <h2 class="font-semibold text-lg">Top customers</h2>
        <a href="{{ route('admin.buyers.index') }}" class="text-sm text-blue-600 hover:underline">View buyers</a>
      </div>
      <div class="space-y-3">
        @forelse($topCustomers as $cust)
          <div class="flex items-center justify-between text-sm">
            <div>
              <div class="font-medium text-gray-800">{{ $cust->full_name ?? 'Guest' }}</div>
              <div class="text-xs text-gray-500">{{ $cust->email }}</div>
            </div>
            <div class="text-right">
              <div class="font-semibold text-gray-800">{{ currency_format($cust->revenue ?? 0) }}</div>
              <div class="text-xs text-gray-500">{{ $cust->orders }} orders</div>
            </div>
          </div>
        @empty
          <p class="text-sm text-gray-500">No customers yet.</p>
        @endforelse
      </div>
    </div>
  </div>

  <div class="bg-white border rounded-xl p-4 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div>
        <h2 class="font-semibold text-lg">Inventory & stock</h2>
        <p class="text-sm text-gray-500">Low stock threshold: {{ $lowStockThreshold }}</p>
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
      <div class="p-3 bg-gray-50 rounded-lg">
        <div class="text-xs text-gray-500">Total products</div>
        <div class="text-xl font-semibold">{{ $stockSummary['total'] ?? 0 }}</div>
      </div>
      <div class="p-3 bg-gray-50 rounded-lg">
        <div class="text-xs text-gray-500">In stock</div>
        <div class="text-xl font-semibold">{{ $stockSummary['inStock'] ?? 0 }}</div>
      </div>
      <div class="p-3 bg-gray-50 rounded-lg">
        <div class="text-xs text-gray-500">Low stock (≤ {{ $lowStockThreshold }})</div>
        <div class="text-xl font-semibold text-amber-700">{{ $stockSummary['lowStock'] ?? 0 }}</div>
      </div>
      <div class="p-3 bg-gray-50 rounded-lg">
        <div class="text-xs text-gray-500">Out of stock</div>
        <div class="text-xl font-semibold text-red-700">{{ $stockSummary['outOfStock'] ?? 0 }}</div>
      </div>
    </div>
    <div class="p-3 bg-gray-50 rounded-lg mb-4">
      <div class="text-xs text-gray-500">Inventory value (price × stock)</div>
      <div class="text-xl font-semibold">{{ currency_format($stockSummary['inventoryValue'] ?? 0) }}</div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div>
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-semibold">Low stock items</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-600">
                <th class="pb-2">Product</th>
                <th class="pb-2">Stock</th>
                <th class="pb-2">Price</th>
                <th class="pb-2">Value</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              @forelse($lowStockItems as $p)
              <tr>
                <td class="py-2">{{ $p->name }}</td>
                <td class="py-2 font-semibold text-amber-700">{{ $p->stock }}</td>
                <td class="py-2">{{ currency_format($p->price) }}</td>
                <td class="py-2">{{ currency_format(($p->price ?? 0) * ($p->stock ?? 0)) }}</td>
              </tr>
              @empty
              <tr><td colspan="4" class="py-3 text-gray-500">No low stock items.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-semibold">Out of stock</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-600">
                <th class="pb-2">Product</th>
                <th class="pb-2">Stock</th>
                <th class="pb-2">Updated</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              @forelse($outOfStockItems as $p)
              <tr>
                <td class="py-2">{{ $p->name }}</td>
                <td class="py-2 font-semibold text-red-700">{{ $p->stock }}</td>
                <td class="py-2 text-xs text-gray-500">{{ $p->updated_at?->diffForHumans() }}</td>
              </tr>
              @empty
              <tr><td colspan="3" class="py-3 text-gray-500">No out of stock items.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
