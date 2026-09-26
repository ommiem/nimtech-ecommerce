@extends('theme::layouts.app')

@php
  $settings = \App\Models\Setting::getCached();
  $siteName = data_get($settings, 'site_name', config('app.name', 'Shoply'));
  $homeUrl = canonical_url('/');
  $organizationSchema = array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    '@id' => $homeUrl.'#organization',
    'name' => $siteName,
    'url' => $homeUrl,
    'logo' => !empty($settings?->logo_path) ? asset('storage/'.$settings->logo_path) : null,
    'email' => data_get($settings, 'contact_email'),
    'telephone' => data_get($settings, 'contact_phone'),
    'address' => !empty(data_get($settings, 'contact_address'))
      ? [
          '@type' => 'PostalAddress',
          'streetAddress' => data_get($settings, 'contact_address'),
          'addressCountry' => 'KE',
        ]
      : null,
    'areaServed' => [
      '@type' => 'Country',
      'name' => 'Kenya',
    ],
  ], fn ($value) => !is_null($value));
  $websiteSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    '@id' => $homeUrl.'#website',
    'name' => $siteName,
    'url' => $homeUrl,
    'inLanguage' => 'en-KE',
    'potentialAction' => [
      '@type' => 'SearchAction',
      'target' => [
        '@type' => 'EntryPoint',
        'urlTemplate' => canonical_url('/products?q={search_term_string}'),
      ],
      'query-input' => 'required name=search_term_string',
    ],
  ];
@endphp
@section('meta_title', 'Phones, Laptops and Electronics in Kenya | '.$siteName)
@section('meta_description', 'Buy phones, laptops, TVs and electronics in Nairobi and across Kenya from '.$siteName.'. Competitive prices, warranty support and fast delivery.')
@section('canonical_url', '/')
@section('meta')
<script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection
@section('content')
  <!-- Argos-inspired: prominent on-page search and quick links -->
  <section class="mt-4 bg-white border rounded">
    <div class="max-w-7xl mx-auto px-4 py-6">
      <h1 class="text-2xl md:text-3xl font-semibold">Phones, Laptops and Electronics in Kenya</h1>
      <form action="{{ route('products.index') }}" method="GET" class="mt-3 relative">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products, categories, brandsâ€¦" class="w-full border rounded-full pl-4 pr-32 py-3 text-base focus:ring-2 focus:ring-blue-500" />
        <button class="absolute right-2 top-1/2 -translate-y-1/2 px-4 py-2 bg-blue-600 text-white rounded-full text-sm">Search</button>
      </form>
      @if(($categories ?? collect())->count())
        <div class="mt-3 flex flex-wrap gap-2 text-sm">
          @foreach(($categories ?? collect()) as $cat)
            <a href="{{ route('categories.show', $cat->canonical_slug) }}" class="px-3 py-1.5 rounded-full border hover:bg-gray-50">{{ $cat->name }}</a>
          @endforeach
        </div>
      @endif
    </div>
  </section>

  <!-- Optional slider just under hero -->
  <div class="mt-4">
    <x-banner-slider :slides="$slides" />
  </div>

  <!-- Promo tiles: quick value props -->
  <section class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white border rounded p-4 flex items-center gap-3">
      <div class="shrink-0 text-blue-600">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
      </div>
      <div>
        <div class="font-semibold">Wide selection</div>
        <div class="text-sm text-gray-600">Shop top categories and brands</div>
      </div>
    </div>
    <div class="bg-white border rounded p-4 flex items-center gap-3">
      <div class="shrink-0 text-blue-600">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2.25M3 7.5h2.25M3 12h2.25M3 16.5h2.25M3 21h2.25M7.5 3H21M7.5 7.5H21M7.5 12H21M7.5 16.5H21M7.5 21H21"/></svg>
      </div>
      <div>
        <div class="font-semibold">Great deals</div>
        <div class="text-sm text-gray-600">New offers every week</div>
      </div>
    </div>
    <div class="bg-white border rounded p-4 flex items-center gap-3">
      <div class="shrink-0 text-blue-600">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75V21h15V9.75"/></svg>
      </div>
      <div>
        <div class="font-semibold">Fast delivery</div>
        <div class="text-sm text-gray-600">Across Kenya on most items</div>
      </div>
    </div>
  </section>

  @include('partials.home-campaigns')

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




