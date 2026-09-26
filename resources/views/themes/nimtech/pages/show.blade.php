@extends('theme::layouts.app')

@php($siteName = optional(\App\Models\Setting::getCached())->site_name ?? config('app.name','Shoply'))
@section('meta_title', $page->title.' | '.$siteName)
@section('meta_description', Str::limit(strip_tags($page->content ?? ''), 150))
@section('canonical_url', route('pages.show', $page))

@if($page->featured_image)
  @section('meta')
    <meta property="og:image" content="{{ asset('storage/'.$page->featured_image) }}">
    <meta name="twitter:image" content="{{ asset('storage/'.$page->featured_image) }}">
  @endsection
  @section('preload')
    <link rel="preload" as="image" href="{{ asset('storage/'.$page->featured_image) }}" fetchpriority="high">
  @endsection
@endif

@section('content')
  <x-breadcrumbs :items="[
    ['label' => 'Home', 'url' => route('home')],
    ['label' => $page->title]
  ]" />

  <section class="relative overflow-hidden rounded border bg-white">
    <div class="absolute inset-0 bg-gradient-to-r from-blue-50 via-white to-red-50"></div>
    <div class="relative p-5 sm:p-6">
      <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600">
        @if($page->category)
          <a href="{{ route('categories.show', $page->category->canonical_slug) }}" class="inline-flex items-center gap-1 rounded-full border border-blue-100 bg-blue-50 px-2 py-1 text-blue-700 hover:bg-blue-100">
            {{ $page->category->name }}
          </a>
        @endif
        @if($page->updated_at)
          <span class="inline-flex items-center gap-1 rounded-full border border-gray-200 bg-white px-2 py-1">
            Updated {{ $page->updated_at->format('M j, Y') }}
          </span>
        @endif
      </div>
      <h1 class="mt-3 text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">{{ $page->title }}</h1>
      @php($pageExcerpt = Str::limit(strip_tags($page->content ?? ''), 170))
      @if(!empty($pageExcerpt))
        <p class="mt-2 text-sm sm:text-base text-gray-600 max-w-3xl">{{ $pageExcerpt }}</p>
      @endif
      @if($page->featured_image && (!$page->category || $categoryProducts->isEmpty()))
        <img src="{{ asset('storage/'.$page->featured_image) }}" alt="{{ $page->title }}" class="mt-4 w-full h-auto rounded border bg-white" loading="eager" fetchpriority="high" decoding="async">
      @endif
    </div>
  </section>

  @if($page->category)
    <section class="mt-8">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-xl font-semibold">More in {{ $page->category->name }}</h2>
          <p class="text-sm text-gray-600">Hand-picked from this category.</p>
        </div>
        <a href="{{ route('categories.show', $page->category->canonical_slug) }}" class="text-sm text-blue-700 hover:underline">View all</a>
      </div>
      @if($categoryProducts->count())
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
          @foreach($categoryProducts as $product)
            <x-product-card :product="$product" />
          @endforeach
        </div>
      @else
        <div class="bg-white border rounded p-6 text-gray-600">No products in this category yet.</div>
      @endif
    </section>
  @endif

  <article class="prose max-w-none bg-white border rounded p-5 sm:p-6 mt-8">
    <div>{!! $page->content !!}</div>
  </article>
@endsection



