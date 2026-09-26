@props(['product'])
@php($isNew = $product->created_at && $product->created_at->gt(now()->subDays(30)))
<div class="group bg-white border rounded overflow-hidden hover:shadow-md transition relative">
    @if($isNew)
      <span class="absolute top-2 left-2 bg-green-600 text-white text-xs px-2 py-0.5 rounded">New</span>
    @endif
    @if($product->stock <= 0)
      <span class="absolute top-2 right-2 bg-gray-700 text-white text-xs px-2 py-0.5 rounded">Out of stock</span>
    @endif
    <a href="{{ route('products.show', $product) }}" class="block overflow-hidden">
        @if($product->image)
            <img src="{{ image_src($product->image) }}" alt="{{ $product->name }}" class="aspect-[4/3] w-full object-cover transition-transform duration-200 group-hover:scale-[1.03]" loading="lazy" decoding="async">
        @else
            <div class="aspect-[4/3] w-full bg-gray-100 flex items-center justify-center text-gray-400">No Image</div>
        @endif
    </a>
    <div class="p-3">
        <a href="{{ route('products.show', $product) }}" class="block text-sm font-medium hover:underline line-clamp-2 min-h-[2.5rem]">{{ $product->name }}</a>
        <div class="mt-1 text-xs text-gray-500 line-clamp-1">{{ $product->category?->name }}</div>
        <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center">
            <div class="text-lg font-bold">{{ currency_format($product->price) }}</div>
        </div>
        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="inline-flex w-full items-center justify-center px-3 py-2 bg-blue-600 text-white rounded text-xs font-semibold hover:bg-blue-700">Add to Cart</button>
            </form>
            <a href="{{ route('products.show', $product) }}" class="inline-flex w-full items-center justify-center px-3 py-2 bg-gray-900 text-white rounded hover:bg-gray-800 text-xs font-semibold">View</a>
        </div>
    </div>
</div>

