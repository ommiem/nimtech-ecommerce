@extends('theme::layouts.app')

@php($siteName = data_get(\App\Models\Setting::getCached(), 'site_name', config('app.name', 'Shoply')))
@section('meta_title', $siteName.' - Deals, New Arrivals, Popular Picks')
@section('meta_description', 'Shop the latest products, top deals, and popular picks at '.$siteName.'.')
@section('content')
  @php($heroProducts = ($newProducts ?? collect())->take(4))
  @php($lowestPrice = ($newProducts ?? collect())->min('price'))
  @php($beautyCategories = \App\Models\Category::query()->orderByDesc('is_featured')->orderByRaw('COALESCE(sort_order, 999999) asc')->orderBy('name')->take(6)->get())

  <section class="an-campaign-hero">
    <div class="an-hero-copy">
      <span class="an-hero-badge">Bodycare &amp; Fragrances - New arrivals every week</span>
      <h1 class="an-hero-title">Glow-ready bodycare, fragrances and daily care essentials.</h1>
      <p class="an-hero-text">Shop lotions, deodorants, fragrances, face care and hair care with WhatsApp support and Kenya delivery.</p>
      <div class="an-hero-actions">
        <a href="{{ route('products.index') }}" class="an-primary-action">Shop new arrivals</a>
        <a href="{{ whatsapp_link('Hi Aspire Noted, I would like help choosing bodycare and fragrance products.') }}" target="_blank" rel="noopener" class="an-secondary-action">Ask on WhatsApp</a>
      </div>
      <div class="an-hero-stats">
        @if($lowestPrice)
          <span>Fresh picks from {{ currency_format($lowestPrice) }}</span>
        @endif
        <span>M-Pesa checkout</span>
        <span>Delivery across Kenya</span>
      </div>
      <form action="{{ route('products.index') }}" method="GET" class="an-hero-search">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products, categories, brands...">
        <button type="submit">Search</button>
      </form>
    </div>
    <div class="an-hero-products">
      @foreach($heroProducts as $product)
        <a href="{{ route('products.show', ['productSlug' => $product->canonical_slug]) }}" class="an-hero-product">
          @if($product->image)
            <img src="{{ image_src($product->image) }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
          @else
            <div class="an-hero-product-media"></div>
          @endif
          <strong>{{ $product->name }}</strong>
          <span>{{ currency_format($product->price) }}</span>
        </a>
      @endforeach
    </div>
  </section>

  <section class="an-trust-strip" aria-label="Shopping benefits">
    <div class="an-trust-item">
      <svg class="an-trust-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path></svg>
      <div><div class="an-trust-title">Original products</div><div class="an-trust-copy">Curated daily care picks</div></div>
    </div>
    <div class="an-trust-item">
      <svg class="an-trust-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm7.5 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM3 6h13.5v9H3V6Zm13.5 3H20l1 2.25V15h-4.5V9Z"></path></svg>
      <div><div class="an-trust-title">Kenya delivery</div><div class="an-trust-copy">Fast dispatch on most items</div></div>
    </div>
    <div class="an-trust-item">
      <svg class="an-trust-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5m-18 3.75h16.5m-15 6h13.5A2.25 2.25 0 0 0 21 15.75v-7.5A2.25 2.25 0 0 0 18.75 6H5.25A2.25 2.25 0 0 0 3 8.25v7.5A2.25 2.25 0 0 0 5.25 18Z"></path></svg>
      <div><div class="an-trust-title">M-Pesa checkout</div><div class="an-trust-copy">Simple secure payment</div></div>
    </div>
    <div class="an-trust-item">
      <svg class="an-trust-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm3.75 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm3.75 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM21 12c0 4.142-4.03 7.5-9 7.5a10.4 10.4 0 0 1-3.238-.509L3 21l1.57-4.186C3.584 15.49 3 13.838 3 12c0-4.142 4.03-7.5 9-7.5s9 3.358 9 7.5Z"></path></svg>
      <div><div class="an-trust-title">WhatsApp help</div><div class="an-trust-copy">Ask before you buy</div></div>
    </div>
  </section>

  @if($beautyCategories->count())
  <section class="an-category-campaigns">
    <div class="an-category-head">
      <div>
        <h2 class="text-xl font-semibold">Shop by beauty need</h2>
        <p class="text-sm text-gray-600">Jump into the categories customers browse most.</p>
      </div>
      <a href="{{ route('products.index') }}" class="text-sm text-blue-700 hover:underline">View all</a>
    </div>
    <div class="an-category-grid">
      @foreach($beautyCategories as $cat)
        @php($catThumb = $cat->image_path ?: $cat->products()->whereNotNull('image')->latest('id')->value('image'))
        <a href="{{ route('categories.show', $cat->canonical_slug) }}" class="an-category-tile {{ $catThumb ? '' : 'an-category-tile--plain' }}">
          @if($catThumb)
            <img src="{{ image_src($catThumb) }}" alt="{{ $cat->name }}" loading="lazy" decoding="async">
          @endif
          <span class="an-category-label">{{ $cat->name }}<small>Shop now</small></span>
        </a>
      @endforeach
    </div>
  </section>
  @endif

  <!-- Optional slider just under hero -->
  <div class="mt-4">
    <x-banner-slider :slides="$slides" />
  </div>

  <!-- Product highlights -->
  <section class="mt-8">
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-xl font-semibold">Top picks for you</h2>
      <a href="{{ route('products.index') }}" class="group inline-flex items-center gap-1 text-sm text-blue-700 hover:underline">
        <span>Browse all products</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-4 h-4 transition-transform group-hover:translate-x-0.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
      </a>
    </div>
    @if($newProducts->count())
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
        @foreach($newProducts as $product)
          <x-product-card :product="$product" />
        @endforeach
      </div>
    @else
      <div class="bg-white border rounded p-6 text-gray-600">No products yet.</div>
    @endif
  </section>

  @if(($popularProducts ?? collect())->count())
  <section class="mt-10">
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-xl font-semibold">Popular now</h2>
      <a href="{{ route('products.index') }}" class="text-sm text-blue-700 hover:underline">Shop all</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
      @foreach($popularProducts as $product)
        <x-product-card :product="$product" />
      @endforeach
    </div>
  </section>
  @endif

  @if(($showDeals ?? false) && ($budgetProducts ?? collect())->count())
  <section class="mt-10">
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-xl font-semibold">Top deals under {{ currency_format($dealsThreshold) }}</h2>
      <a href="{{ route('products.index', ['price_max' => $dealsThreshold]) }}" class="text-sm text-blue-700 hover:underline">View more</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
      @foreach($budgetProducts as $product)
        <x-product-card :product="$product" />
      @endforeach
    </div>
  </section>
  @endif
@endsection





