@extends('theme::layouts.app')
@php
  $siteName = data_get(\App\Models\Setting::getCached(), 'site_name', 'Nimtech');
  $description = $intro.' Prices and availability come from current store records and may change.';
@endphp
@section('meta_title', $heading.' | '.$siteName)
@section('meta_description', $description)
@section('canonical_url', url()->current())
@section('robots', request()->query() ? 'noindex, follow' : 'index, follow')
@section('meta')
<script type="application/ld+json">{!! json_encode([
  '@context' => 'https://schema.org', '@type' => 'ItemList', 'name' => $heading,
  'numberOfItems' => $products->total(),
  'itemListElement' => $products->getCollection()->values()->map(fn($product, $index) => [
    '@type' => 'ListItem', 'position' => $index + 1 + (($products->currentPage() - 1) * $products->perPage()),
    'url' => route('products.show', ['productSlug' => $product->canonical_slug]), 'name' => $product->name,
  ])->all(),
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endsection
@section('content')
<x-breadcrumbs :items="[['label'=>'Home','url'=>route('home')],['label'=>$heading]]" />
<header class="mt-6 rounded-2xl bg-slate-950 px-5 py-8 text-white sm:px-8">
  <p class="text-sm font-semibold uppercase tracking-widest text-white">Live Kenya catalogue</p>
  <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ $heading }}</h1>
  <p class="mt-3 max-w-3xl text-slate-200">{{ $intro }}</p>
  <div class="mt-5 flex flex-wrap gap-3 text-sm">
    <span class="rounded-full bg-white/10 px-3 py-1.5">{{ number_format($products->total()) }} in-stock results</span>
    @if($minPrice !== null)<span class="rounded-full bg-white/10 px-3 py-1.5">From {{ currency_format($minPrice) }}</span>@endif
    <span class="rounded-full bg-white/10 px-3 py-1.5">Updated {{ now()->format('j M Y') }}</span>
  </div>
</header>
<section class="mt-8 overflow-hidden rounded-xl border bg-white">
  <div class="overflow-x-auto"><table class="min-w-full text-left text-sm">
    <thead class="bg-slate-100 text-slate-700"><tr><th class="px-4 py-3">Product</th><th class="px-4 py-3">Brand</th><th class="px-4 py-3">Price</th><th class="px-4 py-3">Stock</th><th class="px-4 py-3">Updated</th><th class="px-4 py-3"><span class="sr-only">View</span></th></tr></thead>
    <tbody class="divide-y">@forelse($products as $product)<tr>
      <td class="px-4 py-3 font-semibold"><a class="text-blue-700 hover:underline" href="{{ route('products.show', ['productSlug'=>$product->canonical_slug]) }}">{{ $product->name }}</a></td>
      <td class="px-4 py-3">{{ $product->brand?->name ?: 'Not specified' }}</td>
      <td class="whitespace-nowrap px-4 py-3 font-bold">{{ currency_format($product->price) }}</td>
      <td class="px-4 py-3">{{ $product->stock > 0 ? 'In stock' : 'Out of stock' }}</td>
      <td class="whitespace-nowrap px-4 py-3">{{ $product->updated_at?->format('j M Y') }}</td>
      <td class="px-4 py-3"><a class="rounded bg-blue-600 px-3 py-2 text-white" href="{{ route('products.show', ['productSlug'=>$product->canonical_slug]) }}">View</a></td>
    </tr>@empty<tr><td colspan="6" class="px-4 py-12 text-center text-gray-600">No matching products are currently in stock. Check back after the next stock update.</td></tr>@endforelse</tbody>
  </table></div>
</section>
<div class="mt-6">{{ $products->links() }}</div>
<section class="mt-10 rounded-xl border bg-white p-6">
  <h2 class="text-2xl font-semibold">How to use this live price page</h2>
  <p class="mt-3 text-gray-700">Prices, availability and update dates come from the Nimtech catalogue. Open a product for its current details, warranty guidance and delivery options. Confirm colour, storage, condition and exact stock with our team before payment because individual variants can sell out.</p>
  <div class="mt-5 flex flex-wrap gap-3">
    <a class="rounded border px-4 py-2 hover:bg-gray-50" href="{{ route('seo.catalog', 'phone-prices-in-kenya') }}">Phone prices</a>
    <a class="rounded border px-4 py-2 hover:bg-gray-50" href="{{ route('seo.catalog', 'laptop-prices-in-kenya') }}">Laptop prices</a>
    <a class="rounded border px-4 py-2 hover:bg-gray-50" href="{{ route('seo.updated') }}">Recently updated</a>
    <a class="rounded border px-4 py-2 hover:bg-gray-50" href="{{ route('seo.compare') }}">Compare products</a>
  </div>
</section>
@endsection
