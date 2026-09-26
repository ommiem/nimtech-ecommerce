@props(['product'])
@php($isNew = $product->created_at && $product->created_at->gt(now()->subDays(30)))
<div class="group relative overflow-hidden rounded border bg-white transition hover:-translate-y-0.5 hover:border-red-200 hover:shadow-md">
    @if($isNew)
      <span class="absolute left-2 top-2 z-10 rounded bg-green-600 px-2 py-0.5 text-xs text-white">New</span>
    @endif
    @if($product->stock <= 0)
      <span class="absolute right-2 top-2 z-10 rounded bg-gray-800 px-2 py-0.5 text-xs text-white">Out of stock</span>
    @endif
    <a href="{{ route('products.show', ['productSlug' => $product->canonical_slug]) }}" class="block overflow-hidden">
        @if($product->image)
            <img src="{{ image_src($product->image) }}" alt="{{ $product->name }}" class="aspect-[4/3] w-full bg-gray-100 object-cover transition-transform duration-300 group-hover:scale-[1.04]" loading="lazy" decoding="async">
        @else
            <div class="flex aspect-[4/3] w-full items-center justify-center bg-gray-100 text-sm text-gray-400">No Image</div>
        @endif
    </a>
    <div class="p-3">
        <a href="{{ route('products.show', ['productSlug' => $product->canonical_slug]) }}" class="block min-h-[2.5rem] text-sm font-semibold leading-5 text-gray-950 line-clamp-2 hover:underline">{{ $product->name }}</a>
        <div class="mt-1 text-xs text-gray-500 line-clamp-1">{{ $product->category?->name }}</div>
        <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center">
            <div class="text-lg font-bold text-gray-950">{{ currency_format($product->price) }}</div>
            <div class="sm:ml-auto">
                <a href="{{ route('products.show', ['productSlug' => $product->canonical_slug]) }}" class="inline-flex w-full items-center justify-center rounded bg-gray-950 px-3 py-1.5 text-xs font-medium text-white hover:bg-gray-800 sm:w-auto">View</a>
            </div>
        </div>
    </div>
</div>

