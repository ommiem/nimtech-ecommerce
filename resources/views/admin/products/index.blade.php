@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-semibold">Products</h1>
  <a href="{{ route('admin.products.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">New Product</a>
</div>

<form method="GET" class="mb-3">
  <input type="text" name="q" value="{{ request('q') }}" placeholder="Search..." class="border rounded px-3 py-2">
  <button class="px-3 py-2 bg-gray-200 rounded">Filter</button>
  @if(request('q'))
  <a class="ml-2 text-sm text-gray-600 hover:underline" href="{{ route('admin.products.index') }}">Clear</a>
  @endif
  </form>

@if(session('deleted_product_id'))
  <div class="mb-3 p-3 bg-yellow-100 text-yellow-900 rounded">
    Product deleted. 
    <form action="{{ route('admin.products.restore', session('deleted_product_id')) }}" method="POST" class="inline">
      @csrf
      <button class="underline font-semibold">Undo</button>
    </form>
  </div>
@endif

<div class="mb-3 space-x-2">
  <a href="{{ route('admin.products.index') }}" class="px-2 py-1 border rounded {{ request('trashed') ? '' : 'bg-gray-200' }}">Active</a>
  <a href="{{ route('admin.products.index', ['trashed' => 1]) }}" class="px-2 py-1 border rounded {{ request('trashed') ? 'bg-gray-200' : '' }}">Trashed</a>
</div>

<div class="bg-white border rounded">
  <table class="min-w-full">
    <thead>
      <tr class="bg-gray-100 text-left">
        <th class="p-3 border">Image</th>
        <th class="p-3 border">Name</th>
        <th class="p-3 border">Category</th>
        <th class="p-3 border">Price</th>
        <th class="p-3 border">Stock</th>
        <th class="p-3 border">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($products as $product)
      <tr>
        <td class="p-3 border">
          @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" class="h-12 w-12 object-cover rounded border" alt="{{ $product->name }}">
          @endif
        </td>
        <td class="p-3 border">{{ $product->name }} @if($product->trashed()) <span class="ml-1 text-xs text-gray-500">(trashed)</span>@endif</td>
        <td class="p-3 border">{{ $product->category?->name }}</td>
        <td class="p-3 border">{{ currency_format($product->price) }}</td>
        <td class="p-3 border">{{ $product->stock }}</td>
        <td class="p-3 border space-x-2">
          @if(!$product->trashed())
            <a class="text-blue-700 hover:underline" href="{{ route('admin.products.show', $product) }}">View</a>
            <a class="text-blue-700 hover:underline" href="{{ route('admin.products.edit', $product) }}">Edit</a>
            <form class="inline" action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Move this product to trash?')">
              @csrf
              @method('DELETE')
              <button class="text-red-700 hover:underline">Delete</button>
            </form>
          @else
            <form class="inline" action="{{ route('admin.products.restore', $product->id) }}" method="POST">
              @csrf
              <button class="text-green-700 hover:underline">Restore</button>
            </form>
            <form class="inline" action="{{ route('admin.products.force-delete', $product->id) }}" method="POST" onsubmit="return confirm('Permanently delete this product? This cannot be undone.')">
              @csrf
              @method('DELETE')
              <button class="text-red-700 hover:underline">Delete Permanently</button>
            </form>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $products->links() }}</div>
@endsection
