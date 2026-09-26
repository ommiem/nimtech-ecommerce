@extends('layouts.admin')

@section('content')
<div>
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Buyers</h1>
    <form method="GET" action="{{ route('admin.buyers.index') }}" class="relative">
      <input type="text" name="q" value="{{ $q }}" placeholder="Search name or email..." class="w-64 border rounded pl-3 pr-9 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
      <button class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500" aria-label="Search">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 3a7.5 7.5 0 105.236 12.764l3.75 3.75a.75.75 0 101.06-1.06l-3.75-3.75A7.5 7.5 0 0010.5 3z"/></svg>
      </button>
    </form>
  </div>

  <div class="bg-white border rounded">
    <table class="min-w-full">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="p-3 border">Name</th>
          <th class="p-3 border">Email</th>
          <th class="p-3 border">Phone</th>
          <th class="p-3 border">Orders</th>
          <th class="p-3 border">Joined</th>
        </tr>
      </thead>
      <tbody>
      @forelse($buyers as $u)
        <tr>
          <td class="p-3 border">{{ $u->name }}</td>
          <td class="p-3 border text-gray-700">{{ $u->email }}</td>
          <td class="p-3 border text-gray-700">{{ $u->defaultAddress?->phone ?? $u->latestAddress?->phone ?? $u->latestOrder?->phone ?? '-' }}</td>
          <td class="p-3 border">{{ $u->orders_count ?? 0 }}</td>
          <td class="p-3 border text-gray-600">{{ optional($u->created_at)->format('Y-m-d') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="p-4 text-center text-gray-500">No buyers found.</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $buyers->links() }}</div>
</div>
@endsection
