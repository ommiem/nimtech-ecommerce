@extends('layouts.admin')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <p class="text-sm text-gray-500">Product</p>
      <h1 class="text-2xl font-semibold">{{ $product->name }}</h1>
      <p class="text-sm text-gray-500">{{ $product->category?->name ?? 'Uncategorized' }} @if($product->brand) • {{ $product->brand?->name }} @endif</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('admin.products.edit', $product) }}" class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Edit</a>
      <a href="{{ route('admin.products.index') }}" class="px-3 py-2 bg-gray-200 rounded text-sm">Back</a>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white border rounded-xl p-4 shadow-sm">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <div class="text-xs text-gray-500">Price</div>
          <div class="text-2xl font-semibold">{{ currency_format($product->price) }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500">Stock</div>
          <div class="text-2xl font-semibold {{ $product->stock <= 3 ? 'text-amber-700' : '' }}">{{ $product->stock }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500">Category</div>
          <div class="font-semibold">{{ $product->category?->name ?? '—' }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500">Brand</div>
          <div class="font-semibold">{{ $product->brand?->name ?? '—' }}</div>
        </div>
      </div>

      <div class="mt-4">
        <h2 class="font-semibold mb-2">Description</h2>
        <div class="prose prose-sm max-w-none text-gray-800 whitespace-pre-line">{{ $product->description ?? 'No description.' }}</div>
      </div>
    </div>

    <div class="bg-white border rounded-xl p-4 shadow-sm">
      <h2 class="font-semibold mb-3">Images</h2>
      <div class="grid grid-cols-2 gap-3">
        @if($product->image)
          <div>
            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="w-full h-28 object-cover rounded border">
            <div class="text-xs text-gray-500 mt-1">Primary</div>
          </div>
        @endif
        @forelse($product->images as $img)
          <div>
            <img src="{{ asset('storage/'.$img->path) }}" alt="{{ $product->name }}" class="w-full h-28 object-cover rounded border">
          </div>
        @empty
          @if(!$product->image)
            <p class="text-sm text-gray-500 col-span-2">No images uploaded.</p>
          @endif
        @endforelse
      </div>
    </div>
  </div>

  <div class="bg-white border rounded-xl p-4 shadow-sm">
    <h2 class="font-semibold mb-3">Meta</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700">
      <div>
        <div class="text-xs text-gray-500">Slug</div>
        <div class="font-mono">{{ $product->slug }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Created</div>
        <div>{{ $product->created_at?->format('M j, Y g:i A') }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Updated</div>
        <div>{{ $product->updated_at?->format('M j, Y g:i A') }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
