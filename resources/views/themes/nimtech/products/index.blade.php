@extends('theme::layouts.app')

@php
  $siteName = data_get(\App\Models\Setting::getCached(), 'site_name', config('app.name', 'Shoply'));
  $brandName = optional(($brands ?? collect())->firstWhere('slug', $brandSlug))->name;
  $pageHeading = 'Phones, Laptops and Electronics in Kenya';
  $pageIntro = 'Explore in-stock phones, laptops, TVs and accessories with transparent pricing, warranty support, and delivery in Nairobi and across Kenya.';
  $title = 'Shop Phones, Laptops and Electronics in Kenya';
  $desc = 'Browse phones, laptops, TVs and electronics in Nairobi and across Kenya at '.$siteName.'. Compare prices, verified stock, warranty support, and fast delivery on in-stock products.';

  if (!empty($currentCategory?->name)) {
    $pageHeading = 'Buy '.$currentCategory->name.' in Kenya';
    $pageIntro = 'Compare '.$currentCategory->name.' prices, verified stock, and warranty support from '.$siteName.' with delivery in Nairobi and across Kenya.';
    $title = 'Buy '.$currentCategory->name.' in Kenya';
    $desc = 'Buy '.$currentCategory->name.' in Kenya at '.$siteName.'. Compare prices, trusted brands, warranty support, and fast delivery in Nairobi and across Kenya.';
  }

  if (!empty($brandName) && !empty($currentCategory?->name)) {
    $pageHeading = 'Buy '.$brandName.' '.$currentCategory->name.' in Kenya';
    $pageIntro = 'Compare '.$brandName.' '.$currentCategory->name.' prices, verified stock, and warranty support from '.$siteName.' with delivery in Nairobi and across Kenya.';
    $title = 'Buy '.$brandName.' '.$currentCategory->name.' in Kenya';
    $desc = 'Buy '.$brandName.' '.$currentCategory->name.' in Kenya at '.$siteName.'. Compare prices, verified stock, warranty support, and fast delivery in Nairobi and across Kenya.';
  } elseif (!empty($brandName)) {
    $pageHeading = 'Buy '.$brandName.' electronics in Kenya';
    $pageIntro = 'Compare '.$brandName.' electronics, verified stock, and warranty support from '.$siteName.' with delivery in Nairobi and across Kenya.';
    $title = 'Buy '.$brandName.' Electronics in Kenya';
    $desc = 'Shop '.$brandName.' electronics in Kenya at '.$siteName.'. Compare prices, verified stock, warranty support, and fast delivery in Nairobi and across Kenya.';
  }

  if (!empty($q)) {
    $pageHeading = 'Search results for '.$q.' in Kenya';
    $pageIntro = 'Compare matching products, live prices, stock availability, and delivery options for '.$q.' at '.$siteName.'.';
    $title = 'Search '.$q.' in Kenya';
    $desc = 'Search '.$q.' at '.$siteName.' and compare phones, laptops, TVs and electronics in Kenya. Check prices, stock availability, warranty support, and delivery options.';
  }

  $filters = [];
  if (is_numeric($priceMin) && is_numeric($priceMax)) {
    $filters[] = 'Price range KES '.number_format((float) $priceMin).' to KES '.number_format((float) $priceMax);
  } elseif (is_numeric($priceMax)) {
    $filters[] = 'Under KES '.number_format((float) $priceMax);
  } elseif (is_numeric($priceMin)) {
    $filters[] = 'Over KES '.number_format((float) $priceMin);
  }

  if ($filters) {
    $filterText = implode('. ', $filters).'.';
    $desc .= ' '.$filterText;
    $pageIntro .= ' '.$filterText;
  }

  $fullTitle = $title.' | '.$siteName;
  $resultsLabel = number_format($products->total()).' results';
@endphp
@section('meta_title', $fullTitle)
@section('meta_description', $desc)
@section('canonical_url', $currentCategory ? route('categories.show', $currentCategory->canonical_slug) : route('products.index'))
@section('robots', (request()->query() || ($currentCategory && $products->total() === 0)) ? 'noindex, follow' : 'index, follow')
@section('content')
<div x-data="{filtersOpen:false}">
<x-banner-slider :slides="$slides" />

<x-breadcrumbs :items="[
  ['label' => 'Home', 'url' => route('home')],
  ['label' => $currentCategory ? ('Category: '.$currentCategory->name) : 'Products']
]" />

<section class="mt-6 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div class="max-w-3xl">
    <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight">{{ $pageHeading }}</h1>
    <p class="mt-2 text-sm sm:text-base text-gray-600">{{ $pageIntro }}</p>
  </div>
  <div class="text-sm text-gray-600">{{ $resultsLabel }}</div>
</section>

<div class="mt-6 flex items-center justify-between gap-4">
  <button type="button" class="md:hidden px-3 py-2 border rounded hover:bg-gray-50" @click="filtersOpen=true">Open Filters</button>
  <form method="GET" class="hidden md:flex items-end gap-3 flex-wrap">
    <div>
      <label class="block text-xs text-gray-600">Sort</label>
      <select name="sort" class="border rounded px-2 py-1">
        <option value="latest" @selected(($sort ?? 'latest')==='latest')>Newest</option>
        <option value="price_asc" @selected(($sort ?? '')==='price_asc')>Price: Low to High</option>
        <option value="price_desc" @selected(($sort ?? '')==='price_desc')>Price: High to Low</option>
        <option value="name_asc" @selected(($sort ?? '')==='name_asc')>Name A–Z</option>
      </select>
    </div>
    <div>
      <label class="block text-xs text-gray-600">Min Price</label>
      <input type="number" step="0.01" name="price_min" class="border rounded px-2 py-1 w-28" value="{{ $priceMin }}">
    </div>
    <div>
      <label class="block text-xs text-gray-600">Max Price</label>
      <input type="number" step="0.01" name="price_max" class="border rounded px-2 py-1 w-28" value="{{ $priceMax }}">
    </div>
    <div>
      <label class="block text-xs text-gray-600">Per Page</label>
      <select name="per_page" class="border rounded px-2 py-1">
        @foreach([12,24,36,48] as $pp)
          <option value="{{ $pp }}" @selected(($perPage ?? 12)===$pp)>{{ $pp }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-xs text-transparent">_</label>
      <button class="px-3 py-1.5 bg-gray-800 text-white rounded">Apply</button>
    </div>
  </form>
</div>
  
  <!-- Mobile Filters Drawer -->
  <div x-show="filtersOpen" x-transition.opacity class="fixed inset-0 z-50 md:hidden">
    <div class="absolute inset-0 bg-black/50" @click="filtersOpen=false"></div>
    <div class="absolute left-0 top-0 h-full w-80 max-w-full bg-white shadow-xl p-4 overflow-y-auto">
      <div class="flex items-center justify-between mb-4">
        <div class="font-semibold">Filters</div>
        <button class="text-2xl" @click="filtersOpen=false" aria-label="Close">&times;</button>
      </div>
      <form method="GET" class="space-y-4">
        <div>
          <label class="block text-xs text-gray-600">Sort</label>
          <select name="sort" class="border rounded px-2 py-1 w-full">
            <option value="latest" @selected(($sort ?? 'latest')==='latest')>Newest</option>
            <option value="price_asc" @selected(($sort ?? '')==='price_asc')>Price: Low to High</option>
            <option value="price_desc" @selected(($sort ?? '')==='price_desc')>Price: High to Low</option>
            <option value="name_asc" @selected(($sort ?? '')==='name_asc')>Name A–Z</option>
          </select>
        </div>
        <div>
          <label class="block text-xs text-gray-600">Min Price</label>
          <input type="number" step="0.01" name="price_min" class="border rounded px-2 py-1 w-full" value="{{ $priceMin }}">
        </div>
        <div>
          <label class="block text-xs text-gray-600">Max Price</label>
          <input type="number" step="0.01" name="price_max" class="border rounded px-2 py-1 w-full" value="{{ $priceMax }}">
        </div>
        <div>
          <label class="block text-xs text-gray-600">Per Page</label>
          <select name="per_page" class="border rounded px-2 py-1 w-full">
            @foreach([12,24,36,48] as $pp)
              <option value="{{ $pp }}" @selected(($perPage ?? 12)===$pp)>{{ $pp }}</option>
            @endforeach
          </select>
        </div>
        <div class="flex gap-2 pt-2">
          <button class="flex-1 px-3 py-2 bg-gray-800 text-white rounded">Apply</button>
          <button type="button" class="flex-1 px-3 py-2 border rounded" @click="filtersOpen=false">Close</button>
        </div>
      </form>
    </div>
  </div>

  </div>

<section id="catalog" class="mt-8">
  @if($q)
    <div class="mb-2 text-sm text-gray-600">Showing results for “<strong>{{ $q }}</strong>”. <a class="ml-2 underline" href="{{ $categorySlug ? route('categories.show', $categorySlug) : route('products.index') }}">Clear</a></div>
  @endif

  @if($products->count() === 0)
    <div class="bg-white border rounded p-8 text-center text-gray-600">No products found.</div>
  @else
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      @foreach($products as $product)
        <x-product-card :product="$product" />
      @endforeach
    </div>
    @if($products->hasMorePages())
      <div class="mt-8 flex justify-center">
        <a href="{{ $products->withQueryString()->nextPageUrl() }}" class="px-5 py-2 border rounded text-sm hover:bg-gray-50">Load more</a>
      </div>
    @endif
  @endif
</section>
</div>
@endsection


