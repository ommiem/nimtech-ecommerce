<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
      $settings = \App\Models\Setting::getCached();
      $siteName = data_get($settings, 'site_name', config('app.name', 'Shoply'));
      $defaultMetaTitle = $siteName.' | Buy Phones, Laptops, and Accessories in Kenya';
      $defaultMetaDescription = 'Shop phones, laptops, and accessories in Kenya at '.$siteName.'. Compare prices, get deals, and enjoy fast delivery.';
      $metaTitle = trim((string) $__env->yieldContent('meta_title', $defaultMetaTitle));
      $metaDescription = trim((string) $__env->yieldContent('meta_description', $defaultMetaDescription));
      $canonicalUrl = canonical_url($__env->yieldContent('canonical_url', request()->getRequestUri()));
    @endphp
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @if(!empty($settings->favicon_path))
      <link rel="icon" href="{{ asset('storage/'.$settings->favicon_path) }}" type="image/png">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    @yield('meta')
    @php
      $manifest = public_path('build/manifest.json');
      $hasTheme = false;
      if (file_exists($manifest)) {
        $data = json_decode(file_get_contents($manifest), true) ?? [];
        $hasTheme = isset($data['resources/css/themes/aspirenoted.css']) && isset($data['resources/js/themes/aspirenoted.js']);
      }
    @endphp
    @if($hasTheme)
      @vite(['resources/css/themes/aspirenoted.css', 'resources/js/themes/aspirenoted.js'])
    @else
      @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @php($primary = $settings->theme_color ?? '#0B6FCB')
    <meta name="theme-color" content="{{ $primary }}" />
    <style>
      :root { --brand-color: {{ $primary }}; --brand-accent: #D90B34; }
      .bg-blue-600 { background-color: var(--brand-color) !important; }
      .hover\:bg-blue-700:hover { background-color: var(--brand-color) !important; }
      .ring-blue-500 { --tw-ring-color: var(--brand-color) !important; }
      .focus\:ring-blue-500:focus { --tw-ring-color: var(--brand-color) !important; }
      .text-blue-600 { color: var(--brand-color) !important; }
      .hover\:text-blue-600:hover { color: var(--brand-color) !important; }
      .border-blue-600 { border-color: var(--brand-color) !important; }
      .bg-red-600 { background-color: var(--brand-accent) !important; }
      .text-red-600 { color: var(--brand-accent) !important; }
      .border-red-600 { border-color: var(--brand-accent) !important; }
    </style>
    <script src="https://analytics.ahrefs.com/analytics.js" data-key="u2ZxmcB5OL+usxwN/WCNDA" async></script>
    @include('partials.tracking-head')
</head>
<body class="bg-gray-50 text-gray-900">
    @include('partials.tracking-body')
    @include('theme::partials.header')
    @include('theme::partials.header-categories')
    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>
    @include('theme::partials.bottom-nav')
    @include('theme::partials.whatsapp-widget')
    @include('theme::partials.footer')
</body>
</html>
