@extends('theme::layouts.app')

@section('content')
<div class="max-w-3xl mx-auto text-center py-16">
  <h1 class="text-4xl font-bold">Page not found</h1>
  <p class="mt-2 text-gray-600">The page you’re looking for doesn’t exist or was moved.</p>
  <div class="mt-6 flex items-center justify-center gap-3">
    <a href="{{ route('home') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Go home</a>
    <a href="{{ route('products.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Browse products</a>
  </div>
</div>
@endsection

