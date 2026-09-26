@extends('theme::layouts.app')

@php($siteName = data_get(\App\Models\Setting::getCached(), 'site_name', config('app.name', 'Shoply')))
@section('meta_title', 'Shop by Brand in Kenya | '.$siteName)
@section('meta_description', 'Shop by brand at '.$siteName.'. Discover top brands and products.')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Shop by brand</h1>

@if($brands->isEmpty())
  <div class="bg-white border rounded p-6 text-gray-600">No brands available yet.</div>
@else
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
    @foreach($brands as $b)
      <a href="{{ route('products.index', ['brand' => $b->slug]) }}" class="group bg-white border rounded overflow-hidden hover:shadow-sm text-center">
        <div class="aspect-[4/3] w-full bg-gray-100 flex items-center justify-center">
          @if($b->image_path)
            <img src="{{ image_src($b->image_path) }}" alt="{{ $b->name }}" class="h-full w-full object-contain p-4" loading="lazy" decoding="async">
          @else
            <div class="text-gray-500 font-semibold">{{ $b->name }}</div>
          @endif
        </div>
        <div class="p-3 font-medium group-hover:text-blue-700">{{ $b->name }}</div>
      </a>
    @endforeach
  </div>
@endif
@endsection




