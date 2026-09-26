@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-semibold">Promotions</h1>
  <a href="{{ route('admin.promotions.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">New Promotion</a>
</div>

@if(session('success'))
  <div class="mb-4 p-2 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
@endif

<div class="bg-white border rounded">
  <table class="min-w-full">
    <thead>
      <tr class="bg-gray-100 text-left">
        <th class="p-3 border">Code</th>
        <th class="p-3 border">Type</th>
        <th class="p-3 border">Value</th>
        <th class="p-3 border">Min Subtotal</th>
        <th class="p-3 border">Usage</th>
        <th class="p-3 border">Window</th>
        <th class="p-3 border">Active</th>
        <th class="p-3 border">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($promotions as $promo)
        <tr>
          <td class="p-3 border font-mono text-sm">{{ $promo->code }}</td>
          <td class="p-3 border text-sm">{{ ucfirst($promo->type) }}</td>
          <td class="p-3 border text-sm">
            @if($promo->type === 'percent')
              {{ rtrim(rtrim(number_format($promo->value, 2, '.', ''), '0'), '.') }}%
            @else
              {{ currency_format($promo->value) }}
            @endif
          </td>
          <td class="p-3 border text-sm">
            {{ $promo->min_subtotal !== null ? currency_format($promo->min_subtotal) : '—' }}
          </td>
          <td class="p-3 border text-sm">
            {{ $promo->used }}@if($promo->usage_limit)/{{ $promo->usage_limit }}@endif
          </td>
          <td class="p-3 border text-xs text-gray-600">
            @if($promo->starts_at)
              From {{ $promo->starts_at->format('Y-m-d') }}
            @endif
            @if($promo->ends_at)
              <br>Until {{ $promo->ends_at->format('Y-m-d') }}
            @endif
            @if(!$promo->starts_at && !$promo->ends_at)
              Anytime
            @endif
          </td>
          <td class="p-3 border text-sm">
            @if($promo->active)
              <span class="text-green-700">Active</span>
            @else
              <span class="text-gray-500">Disabled</span>
            @endif
          </td>
          <td class="p-3 border text-sm space-x-2">
            <a href="{{ route('admin.promotions.edit', $promo) }}" class="text-blue-700 hover:underline">Edit</a>
            <form action="{{ route('admin.promotions.destroy', $promo) }}" method="POST" class="inline" onsubmit="return confirm('Delete this promotion? Existing orders keep their discounts.')">
              @csrf
              @method('DELETE')
              <button class="text-red-700 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="p-4 border text-center text-sm text-gray-500">No promotions found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $promotions->links() }}</div>
@endsection

