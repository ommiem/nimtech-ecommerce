@extends('layouts.app')

@section('content')
<x-breadcrumbs :items="[
  ['label' => 'Home', 'url' => route('products.index')],
  ['label' => 'Addresses']
]" />
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
  <h1 class="text-2xl font-semibold">Saved Addresses</h1>
  <a href="{{ route('account.addresses.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded sm:w-auto w-full text-center">Add Address</a>
</div>

@if($addresses->isEmpty())
  <div class="bg-white border rounded p-6 text-gray-600">No saved addresses yet.</div>
@else
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  @foreach($addresses as $a)
    <div class="bg-white border rounded p-4">
      <div class="flex items-start justify-between gap-3">
        <div class="font-semibold">{{ $a->label ?? 'Address' }} @if($a->is_default)<span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Default</span>@endif</div>
        <div class="flex flex-wrap gap-3 text-sm">
          <a class="text-blue-700 hover:underline" href="{{ route('account.addresses.edit', $a) }}">Edit</a>
          <form class="inline" method="POST" action="{{ route('account.addresses.destroy', $a) }}" onsubmit="return confirm('Delete this address?')">
            @csrf @method('DELETE')
            <button class="text-red-700 hover:underline">Delete</button>
          </form>
        </div>
      </div>
      <div class="mt-2 text-sm text-gray-700">
        <div>{{ $a->full_name }}</div>
        <div>{{ $a->address }}</div>
        <div>Town/Area: {{ $a->city ?: '-' }}</div>
        <div>County: {{ $a->state ?: '-' }}</div>
        <div>Postal Code / P.O. Box: {{ $a->postal_code ?: '-' }}</div>
        @if($a->phone)<div>Phone: {{ $a->phone }}</div>@endif
        @if($a->email)<div>Email: {{ $a->email }}</div>@endif
      </div>
    </div>
  @endforeach
  </div>
@endif
@endsection
