@extends('theme::layouts.app')

@php
  $siteName = data_get(\App\Models\Setting::getCached(), 'site_name', config('app.name', 'Shoply'));
  $metaTitle = trim((string) ($campaign->meta_title ?: ($campaign->title.' | '.$siteName)));
  $metaDescription = trim((string) ($campaign->meta_description ?: ($campaign->summary ?: 'Shop curated campaign deals and bundles at '.$siteName.'.')));
  $campaignUrl = route('campaigns.show', $campaign);
  $campaignSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $campaign->title,
    'description' => $metaDescription,
    'url' => $campaignUrl,
    'mainEntity' => [
      '@type' => 'ItemList',
      'itemListElement' => $bundleItems->values()->map(function ($item, $index) {
        return [
          '@type' => 'ListItem',
          'position' => $index + 1,
          'url' => route('products.show', ['productSlug' => $item['product']->canonical_slug]),
          'name' => $item['product']->name,
        ];
      })->all(),
    ],
  ];

  if ($campaign->featured_image) {
    $campaignSchema['image'] = asset('storage/'.$campaign->featured_image);
  }

  $firstBundleItem = $bundleItems->first();
  $campaignHeroImage = null;
  if ($campaign->featured_image) {
    $campaignHeroImage = asset('storage/'.$campaign->featured_image);
  } elseif ($firstBundleItem && !empty($firstBundleItem['product']->image)) {
    $campaignHeroImage = image_src($firstBundleItem['product']->image);
  }
@endphp

@section('meta_title', $metaTitle)
@section('meta_description', $metaDescription)
@section('canonical_url', $campaignUrl)

@section('meta')
@if($campaign->featured_image)
  <meta property="og:image" content="{{ asset('storage/'.$campaign->featured_image) }}">
@endif
<script type="application/ld+json">{!! json_encode($campaignSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<x-breadcrumbs :items="[
  ['label' => 'Home', 'url' => route('home')],
  ['label' => $campaign->title],
]" />

<section class="overflow-hidden rounded border bg-white">
  <div class="grid lg:grid-cols-[minmax(0,0.92fr)_minmax(0,1.08fr)]">
    <div class="flex min-h-[20rem] flex-col justify-center p-5 sm:p-7 lg:min-h-[27rem] lg:pr-10">
      @if($campaign->hero_badge)
        <span class="inline-flex rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">{{ $campaign->hero_badge }}</span>
      @endif
      <h1 class="mt-3 text-3xl sm:text-4xl font-semibold tracking-tight">{{ $campaign->title }}</h1>
      @if($campaign->summary)
        <p class="mt-3 text-sm sm:text-base text-gray-600 max-w-2xl">{{ $campaign->summary }}</p>
      @endif

      <div class="mt-6 flex max-w-xl flex-wrap items-center gap-3">
        @if($campaign->cta_label && $campaign->cta_url)
          <a href="{{ $campaign->cta_url }}" class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">{{ $campaign->cta_label }}</a>
        @endif
        @if($bundleWhatsApp)
          <a href="{{ $bundleWhatsApp }}" target="_blank" rel="noopener" class="inline-flex items-center rounded border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">Ask on WhatsApp</a>
        @endif
      </div>

      @if($campaign->promotion && $campaign->promotion->isActive())
        <div class="mt-4 rounded border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
          Promo code: <strong>{{ $campaign->promotion->code }}</strong>
        </div>
      @endif
    </div>

    <div class="relative min-h-[20rem] overflow-hidden bg-gray-100 lg:min-h-[27rem]">
      @if($campaignHeroImage)
        <img src="{{ $campaignHeroImage }}" alt="{{ $campaign->title }}" class="h-full min-h-[20rem] w-full object-cover lg:min-h-[27rem]">
        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/75 via-black/35 to-transparent p-5 text-white">
          <div class="text-xs uppercase tracking-wide text-white/75">Featured campaign</div>
          <div class="mt-1 text-2xl font-semibold">{{ $campaign->title }}</div>
          <div class="mt-1 text-sm text-white/85">{{ $bundleItems->count() }} selected items · {{ currency_format($bundleTotal) }}</div>
        </div>
      @else
        <div class="flex h-full min-h-[20rem] items-center justify-center p-6 text-center">
          <div>
            <div class="text-xs uppercase tracking-wide text-gray-500">Featured campaign</div>
            <div class="mt-2 text-2xl font-semibold">{{ $campaign->title }}</div>
            <div class="mt-1 text-sm text-gray-600">{{ $bundleItems->count() }} selected items · {{ currency_format($bundleTotal) }}</div>
          </div>
        </div>
      @endif
    </div>
  </div>
</section>

@if($campaign->content)
  <section class="mt-8 rounded border bg-white p-5 sm:p-6">
    <div class="prose max-w-none">{!! nl2br(e($campaign->content)) !!}</div>
  </section>
@endif

@if($bundleItems->count())
  <section class="mt-8">
    <div class="mb-3 flex items-center justify-between gap-3">
      <div>
        <h2 class="text-xl font-semibold">Products in this campaign</h2>
        <p class="text-sm text-gray-600">Curated for this promotion and ready for bundle checkout.</p>
      </div>
      <span class="text-sm text-gray-500">{{ $bundleItems->count() }} items</span>
    </div>
    <div class="grid grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-4">
      @foreach($bundleItems as $item)
        <div>
          <x-product-card :product="$item['product']" />
          <div class="mt-2 rounded border bg-white px-3 py-2 text-xs text-gray-600">
            Bundle quantity: <strong>{{ $item['quantity'] }}</strong>
          </div>
        </div>
      @endforeach
    </div>
  </section>
@endif

@if($campaign->compare_enabled && $bundleItems->count())
  <section class="mt-8 rounded border bg-white p-5 sm:p-6 overflow-x-auto">
    <h2 class="text-xl font-semibold">Quick compare</h2>
    <p class="mt-1 text-sm text-gray-600">A practical side-by-side view for the products in this campaign.</p>

    <table class="mt-4 min-w-full text-sm">
      <thead class="bg-gray-50 text-gray-600">
        <tr>
          <th class="px-3 py-2 text-left">Product</th>
          <th class="px-3 py-2 text-left">Brand</th>
          <th class="px-3 py-2 text-left">Category</th>
          <th class="px-3 py-2 text-left">Price</th>
          <th class="px-3 py-2 text-left">Stock</th>
          <th class="px-3 py-2 text-left">Highlights</th>
        </tr>
      </thead>
      <tbody>
        @foreach($bundleItems as $item)
          <tr class="border-t align-top">
            <td class="px-3 py-3">
              <a href="{{ route('products.show', ['productSlug' => $item['product']->canonical_slug]) }}" class="font-medium hover:underline">{{ $item['product']->name }}</a>
            </td>
            <td class="px-3 py-3">{{ $item['product']->brand?->name ?? 'Unbranded' }}</td>
            <td class="px-3 py-3">{{ $item['product']->category?->name ?? 'General' }}</td>
            <td class="px-3 py-3">{{ currency_format($item['product']->price) }}</td>
            <td class="px-3 py-3">{{ $item['product']->stock > 0 ? $item['product']->stock.' in stock' : 'Out of stock' }}</td>
            <td class="px-3 py-3">
              @if($item['highlights'])
                <ul class="list-disc pl-4 space-y-1 text-gray-600">
                  @foreach($item['highlights'] as $highlight)
                    <li>{{ $highlight }}</li>
                  @endforeach
                </ul>
              @else
                <span class="text-gray-500">See product page for more details.</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </section>
@endif
@endsection
