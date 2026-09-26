@extends('theme::layouts.app')
@section('meta_title', 'Compare Phones and Laptops in Kenya | Nimtech')
@section('meta_description', 'Compare up to four in-stock phones or laptops by price, brand, stock and current product information at Nimtech Kenya.')
@section('canonical_url', route('seo.compare'))
@section('robots', request()->query() ? 'noindex, follow' : 'index, follow')
@section('content')
<x-breadcrumbs :items="[['label'=>'Home','url'=>route('home')],['label'=>'Compare products']]" />
<header class="mt-6"><h1 class="text-3xl font-bold">Compare Phones and Laptops in Kenya</h1><p class="mt-2 max-w-3xl text-gray-600">Select two to four current products to compare catalogue prices, brands and availability. Product pages contain full descriptions and ordering options.</p></header>
<form method="GET" class="mt-6 rounded-xl border bg-white p-5">
  <label class="font-semibold" for="products">Choose products</label>
  <select id="products" name="products[]" multiple size="10" class="mt-2 w-full rounded border p-2">@foreach($candidates as $candidate)<option value="{{ $candidate->id }}" @selected($products->contains('id',$candidate->id))>{{ $candidate->name }} - {{ currency_format($candidate->price) }}</option>@endforeach</select>
  <p class="mt-2 text-xs text-gray-500">Hold Ctrl to select more than one product.</p><button class="mt-4 rounded bg-blue-600 px-5 py-2 text-white">Compare selected</button>
</form>
@if($products->count())<div class="mt-8 overflow-x-auto rounded-xl border bg-white"><table class="min-w-full text-sm"><thead class="bg-slate-100"><tr><th class="px-4 py-3 text-left">Feature</th>@foreach($products as $product)<th class="px-4 py-3 text-left">{{ $product->name }}</th>@endforeach</tr></thead><tbody class="divide-y">
  <tr><th class="px-4 py-3 text-left">Price</th>@foreach($products as $product)<td class="px-4 py-3 font-bold">{{ currency_format($product->price) }}</td>@endforeach</tr>
  <tr><th class="px-4 py-3 text-left">Brand</th>@foreach($products as $product)<td class="px-4 py-3">{{ $product->brand?->name ?: 'Not specified' }}</td>@endforeach</tr>
  <tr><th class="px-4 py-3 text-left">Category</th>@foreach($products as $product)<td class="px-4 py-3">{{ $product->category?->name ?: 'Not specified' }}</td>@endforeach</tr>
  <tr><th class="px-4 py-3 text-left">Availability</th>@foreach($products as $product)<td class="px-4 py-3">{{ $product->stock > 0 ? 'In stock' : 'Out of stock' }}</td>@endforeach</tr>
  <tr><th class="px-4 py-3 text-left">Updated</th>@foreach($products as $product)<td class="px-4 py-3">{{ $product->updated_at?->format('j M Y') }}</td>@endforeach</tr>
  <tr><th class="px-4 py-3 text-left">Details</th>@foreach($products as $product)<td class="px-4 py-3"><a class="text-blue-700 underline" href="{{ route('products.show',['productSlug'=>$product->canonical_slug]) }}">View product</a></td>@endforeach</tr>
</tbody></table></div>@endif
@endsection
