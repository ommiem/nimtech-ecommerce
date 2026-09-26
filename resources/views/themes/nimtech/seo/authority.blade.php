@extends('theme::layouts.app')

@php($siteName = data_get(\App\Models\Setting::getCached(), 'site_name', config('app.name', 'Shoply')))
@php($authorityMetaTitle = !empty($page['meta_title_exact']) ? $page['meta_title'] : ($page['meta_title'].' | '.$siteName))
@section('meta_title', $authorityMetaTitle)
@section('meta_description', $page['meta_description'])

@section('meta')
@if(!empty($faqSchema))
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
@endsection

@section('content')
<x-breadcrumbs :items="[
  ['label' => 'Home', 'url' => route('home')],
  ['label' => $page['heading']]
]" />

<section class="rounded border bg-white p-5 sm:p-6">
  <div class="grid gap-6 md:grid-cols-12">
    <div class="md:col-span-8">
      <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight">{{ $page['heading'] }}</h1>
      <div class="mt-4 space-y-3 text-sm sm:text-base text-gray-700">
        @foreach($page['intro'] ?? [] as $paragraph)
          <p>{{ $paragraph }}</p>
        @endforeach
      </div>

      @if(!empty($page['keyword_targets']))
        <div class="mt-5 flex flex-wrap gap-2">
          @foreach($page['keyword_targets'] as $keyword)
            <span class="inline-flex rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs text-blue-800">{{ $keyword }}</span>
          @endforeach
        </div>
      @endif
    </div>

    <aside class="md:col-span-4 space-y-3">
      <div class="rounded border bg-gray-50 p-3">
        <div class="text-xs uppercase tracking-wide text-gray-500">Warranty</div>
        <p class="mt-1 text-sm text-gray-700">{{ $page['warranty'] ?? 'Warranty information available per product.' }}</p>
      </div>
      <div class="rounded border bg-gray-50 p-3">
        <div class="text-xs uppercase tracking-wide text-gray-500">Delivery Kenya</div>
        <p class="mt-1 text-sm text-gray-700">{{ $page['delivery'] ?? 'Delivery available across Kenya.' }}</p>
      </div>
      <div class="rounded border bg-gray-50 p-3">
        <div class="text-xs uppercase tracking-wide text-gray-500">After-sale support</div>
        <p class="mt-1 text-sm text-gray-700">{{ $page['after_sale'] ?? 'After-sale support available for purchased products.' }}</p>
      </div>
    </aside>
  </div>
</section>

@if(!empty($page['guide_points']))
  <section class="mt-8 rounded border bg-white p-5 sm:p-6">
    <h2 class="text-xl font-semibold">{{ $page['guide_title'] ?? 'Buying guide' }}</h2>
    <ul class="mt-4 grid gap-3 sm:grid-cols-2">
      @foreach($page['guide_points'] as $point)
        <li class="rounded border bg-gray-50 px-3 py-2 text-sm text-gray-700">{{ $point }}</li>
      @endforeach
    </ul>
  </section>
@endif

@if(!empty($page['extended_content']))
  <section class="mt-8 rounded border bg-white p-5 sm:p-6">
    <h2 class="text-xl font-semibold">{{ $page['extended_title'] ?? 'Detailed guide' }}</h2>
    <div class="mt-4 space-y-3 text-sm sm:text-base text-gray-700">
      @foreach($page['extended_content'] as $paragraph)
        <p>{{ $paragraph }}</p>
      @endforeach
    </div>
  </section>
@endif

@if(($segmentBlocks ?? []) && count($segmentBlocks))
  @foreach($segmentBlocks as $segment)
    <section class="mt-8">
      <div class="mb-3">
        <h2 class="text-xl font-semibold">{{ $segment['title'] }}</h2>
        @if(!empty($segment['description']))
          <p class="text-sm text-gray-600 mt-1">{{ $segment['description'] }}</p>
        @endif
      </div>
      @if(($segment['products'] ?? collect())->count())
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6">
          @foreach($segment['products'] as $product)
            <x-product-card :product="$product" />
          @endforeach
        </div>
      @else
        <div class="rounded border bg-white p-5 text-sm text-gray-600">No products matched this section yet.</div>
      @endif
    </section>
  @endforeach
@endif

@if(($budgetProducts ?? collect())->count())
  <section class="mt-8">
    <div class="mb-3">
      <h2 class="text-xl font-semibold">{{ $page['budget_title'] ?? 'Budget-friendly picks' }}</h2>
      @if(!empty($page['budget_description']))
        <p class="text-sm text-gray-600 mt-1">{{ $page['budget_description'] }}</p>
      @endif
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
      @foreach($budgetProducts as $product)
        <x-product-card :product="$product" />
      @endforeach
    </div>
  </section>
@endif

<section class="mt-8">
  <div class="mb-3">
    <h2 class="text-xl font-semibold">Top models available</h2>
    <p class="text-sm text-gray-600 mt-1">Popular picks currently available in this category.</p>
  </div>
  @if($featuredProducts->count())
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
      @foreach($featuredProducts as $product)
        <x-product-card :product="$product" />
      @endforeach
    </div>
  @else
    <div class="rounded border bg-white p-5 text-sm text-gray-600">Products will appear here once stock is published.</div>
  @endif
</section>

@if($affordableProducts->count())
  <section class="mt-8">
    <div class="mb-3">
      <h2 class="text-xl font-semibold">Affordable options</h2>
      <p class="text-sm text-gray-600 mt-1">Entry and mid-range choices with strong value.</p>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
      @foreach($affordableProducts as $product)
        <x-product-card :product="$product" />
      @endforeach
    </div>
  </section>
@endif

@if($premiumProducts->count())
  <section class="mt-8">
    <div class="mb-3">
      <h2 class="text-xl font-semibold">Performance and premium picks</h2>
      <p class="text-sm text-gray-600 mt-1">Higher-spec models for demanding users.</p>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
      @foreach($premiumProducts as $product)
        <x-product-card :product="$product" />
      @endforeach
    </div>
  </section>
@endif

@if(!empty($page['why_buy']))
  <section class="mt-8 rounded border bg-white p-5 sm:p-6">
    <h2 class="text-xl font-semibold">Why buy from Nimtech</h2>
    <ul class="mt-4 grid gap-3 sm:grid-cols-2">
      @foreach($page['why_buy'] as $point)
        <li class="rounded border bg-gray-50 px-3 py-2 text-sm text-gray-700">{{ $point }}</li>
      @endforeach
    </ul>
  </section>
@endif

@if(!empty($page['faq']))
  <section class="mt-8 rounded border bg-white p-5 sm:p-6">
    <h2 class="text-xl font-semibold">Frequently asked questions</h2>
    <div class="mt-4 space-y-3">
      @foreach($page['faq'] as $faqItem)
        <details class="group rounded border bg-gray-50 p-3" @if($loop->first) open @endif>
          <summary class="cursor-pointer list-none font-medium text-gray-900 flex items-center justify-between">
            <span>{{ $faqItem['question'] }}</span>
            <span class="text-xs text-gray-500 group-open:hidden">Open</span>
            <span class="text-xs text-gray-500 hidden group-open:inline">Close</span>
          </summary>
          <p class="mt-2 text-sm text-gray-700">{{ $faqItem['answer'] }}</p>
        </details>
      @endforeach
    </div>
  </section>
@endif

@if(($relatedLinks ?? collect())->count())
  <section class="mt-8 rounded border bg-white p-5 sm:p-6">
    <h2 class="text-xl font-semibold">Explore more</h2>
    <div class="mt-4 flex flex-wrap gap-2">
      @foreach($relatedLinks as $link)
        <a href="{{ $link['url'] }}" class="inline-flex rounded-full border px-3 py-1.5 text-sm hover:bg-gray-50">{{ $link['label'] }}</a>
      @endforeach
    </div>
  </section>
@endif
@endsection
