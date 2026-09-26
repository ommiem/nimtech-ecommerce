<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($settings = \App\Models\Setting::getCached())
    <title>Admin | {{ $settings->site_name ?? config('app.name', 'Shoply') }}</title>
    <meta name="description" content="Admin panel for managing the store.">
    <meta property="og:site_name" content="{{ $settings->site_name ?? 'Shoply' }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Admin | {{ $settings->site_name ?? 'Shoply' }}">
    <meta property="og:description" content="Admin panel for managing the store.">
    @php($canonicalUrl = canonical_url(request()->getRequestUri()))
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @if(!empty($settings->favicon_path))
      <link rel="icon" href="{{ asset('storage/'.$settings->favicon_path) }}" type="image/png">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Admin | {{ $settings->site_name ?? 'Shoply' }}">
    <meta name="twitter:description" content="Admin panel for managing the store.">
    @yield('meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php($primary = $settings->theme_color ?? '#2563eb')
    <meta name="theme-color" content="{{ $primary }}" />
    <style>
      :root { --brand-color: {{ $primary }}; }
      .bg-blue-600 { background-color: var(--brand-color) !important; }
      .hover\:bg-blue-700:hover { background-color: var(--brand-color) !important; }
      .ring-blue-500 { --tw-ring-color: var(--brand-color) !important; }
      .focus\:ring-blue-500:focus { --tw-ring-color: var(--brand-color) !important; }
      .text-blue-600 { color: var(--brand-color) !important; }
      .hover\:text-blue-600:hover { color: var(--brand-color) !important; }
      .border-blue-600 { border-color: var(--brand-color) !important; }
    </style>
  </head>
  <body class="h-full bg-gray-50 text-gray-900" x-data="{mobileNav:false}">
    <header class="bg-white border-b sticky top-0 z-40">
      <div class="px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <button type="button" class="md:hidden p-2 border rounded" @click="mobileNav=true" aria-label="Open admin menu">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
          </button>
          <a href="{{ route('admin.dashboard') }}" class="font-semibold">Admin Panel</a>
          <span class="text-gray-400">/</span>
          <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:underline">{{ $settings->site_name ?? config('app.name', 'Shoply') }}</a>
        </div>
        <div class="flex items-center gap-3">
          <a href="{{ route('products.index') }}" class="hidden sm:inline-block px-3 py-1.5 border rounded text-sm">View Store</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="px-3 py-1.5 bg-blue-600 text-white rounded text-sm">Logout</button>
          </form>
        </div>
      </div>
    </header>

    <div class="min-h-[calc(100vh-56px)] flex">
      <!-- Sidebar (desktop) -->
      <aside class="hidden md:block w-64 bg-white border-r">
        <div class="p-4">
          @include('admin.partials.sidebar')
        </div>
      </aside>

      <!-- Mobile sidebar overlay -->
      <div x-show="mobileNav" style="display:none" class="fixed inset-0 z-50 md:hidden" @keydown.escape.window="mobileNav=false">
        <div class="absolute inset-0 bg-black/30" @click="mobileNav=false"></div>
        <aside class="absolute left-0 top-0 bottom-0 w-72 bg-white shadow-lg p-4">
          <div class="flex items-center justify-between mb-4">
            <div class="font-semibold">Admin Menu</div>
            <button class="p-2" @click="mobileNav=false" aria-label="Close admin menu">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          @include('admin.partials.sidebar')
        </aside>
      </div>

      <!-- Main content -->
      <main class="flex-1 p-4 sm:p-6 lg:p-8">
        @yield('content')
      </main>
    </div>
  </body>
</html>
