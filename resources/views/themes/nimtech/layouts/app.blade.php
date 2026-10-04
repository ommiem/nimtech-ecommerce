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
      $robotsMeta = trim((string) $__env->yieldContent('robots', 'index, follow'));
    @endphp
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="{{ $robotsMeta }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @if(!empty($settings->favicon_path))
      <link rel="icon" href="{{ asset('storage/'.$settings->favicon_path) }}" type="image/png">
    @else
      <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    @yield('meta')
    @yield('preload')
    <script type="application/ld+json">{!! json_encode(array_filter([
      '@context' => 'https://schema.org', '@type' => 'ElectronicsStore',
      'name' => $siteName, 'url' => route('home'),
      'telephone' => $settings?->contact_phone, 'email' => $settings?->contact_email,
      'address' => $settings?->contact_address ? ['@type'=>'PostalAddress','streetAddress'=>$settings->contact_address,'addressCountry'=>'KE'] : null,
    ], fn($value) => !empty($value)), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    @php
      $manifest = public_path('build/manifest.json');
      $hasTheme = false;
      if (file_exists($manifest)) {
        $data = json_decode(file_get_contents($manifest), true) ?? [];
        $hasTheme = isset($data['resources/css/themes/nimtech.css']) && isset($data['resources/js/themes/nimtech.js']);
      }
    @endphp
    @if($hasTheme)
      @vite(['resources/css/themes/nimtech.css', 'resources/js/themes/nimtech.js'])
    @else
      @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link rel="stylesheet" href="{{ asset('brand/nimtech-theme.css') }}">
    @php
      $primary = $settings->theme_color ?? '#2563eb';
    @endphp
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
    <script>
      // Analytics is not needed for the first paint. Loading it after the page has
      // rendered prevents it competing with CSS, the header, and the LCP element.
      window.addEventListener('load', function () {
        var script = document.createElement('script');
        script.src = 'https://analytics.ahrefs.com/analytics.js';
        script.dataset.key = 'u2ZxmcB5OL+usxwN/WCNDA';
        script.async = true;
        document.head.appendChild(script);
      }, { once: true });
    </script>
    @include('partials.tracking-head')
</head>
<body class="bg-gray-50 text-gray-900">
    @include('partials.tracking-body')
    @php
      $topNotice = trim((string) ($settings->header_notice_text ?? ''));
      $topPhone = trim((string) ($settings->contact_phone ?? ''));
      $topPhoneHref = $topPhone !== '' ? preg_replace('/[^0-9+]/', '', $topPhone) : '';
    @endphp
    <div class="border-b border-slate-800 bg-slate-950 text-sm text-slate-100">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-2 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
            <div>{{ $topNotice !== '' ? $topNotice : ('Free delivery for orders over '.currency_format(5000).' - New arrivals every week!') }}</div>
            <div class="flex items-center justify-center gap-3 text-xs sm:text-sm">
                @if($topPhone !== '')
                    <a href="tel:{{ $topPhoneHref }}" class="inline-flex items-center gap-1.5 font-medium text-white hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true"><path fill-rule="evenodd" d="M1.5 4.5A3 3 0 014.5 1.5h1.372c.86 0 1.61.586 1.819 1.42l.513 2.053a1.875 1.875 0 01-.49 1.748L6.8 7.635a12.75 12.75 0 005.565 5.565l.914-.914a1.875 1.875 0 011.748-.49l2.053.513a1.875 1.875 0 011.42 1.819V15.5a3 3 0 01-3 3h-.75C7.43 18.5 1.5 12.57 1.5 5.25V4.5z" clip-rule="evenodd"/></svg>
                        <span>Call {{ $topPhone }}</span>
                    </a>
                @endif
                <a href="{{ whatsapp_link('Hello! I have a question about your products') }}" class="inline-flex items-center gap-1.5 font-medium text-green-300 hover:text-green-200 hover:underline">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="currentColor" class="h-4 w-4" aria-hidden="true"><path d="M16.04 3.5C9.19 3.5 3.62 9.06 3.62 15.9c0 2.19.57 4.33 1.66 6.2L3.5 28.5l6.55-1.72a12.34 12.34 0 005.99 1.53h.01c6.84 0 12.41-5.56 12.41-12.4S22.89 3.5 16.04 3.5zm0 22.71h-.01a10.29 10.29 0 01-5.24-1.43l-.38-.23-3.89 1.02 1.04-3.79-.25-.39a10.26 10.26 0 01-1.57-5.49c0-5.68 4.62-10.3 10.31-10.3 2.75 0 5.34 1.07 7.29 3.02a10.23 10.23 0 013.02 7.29c0 5.68-4.63 10.3-10.32 10.3zm5.65-7.72c-.31-.16-1.83-.9-2.11-1-.28-.1-.49-.16-.69.16-.21.31-.8 1-.98 1.2-.18.21-.36.23-.67.08-.31-.16-1.31-.48-2.49-1.54-.92-.82-1.54-1.83-1.72-2.14-.18-.31-.02-.48.14-.63.14-.14.31-.36.47-.54.16-.18.21-.31.31-.52.1-.21.05-.39-.03-.54-.08-.16-.69-1.67-.95-2.29-.25-.6-.5-.52-.69-.53h-.59c-.21 0-.54.08-.82.39-.28.31-1.08 1.05-1.08 2.57 0 1.51 1.11 2.98 1.26 3.18.16.21 2.18 3.33 5.28 4.67.74.32 1.31.51 1.76.65.74.23 1.42.2 1.95.12.6-.09 1.83-.75 2.09-1.47.26-.72.26-1.34.18-1.47-.08-.13-.28-.21-.59-.36z"/></svg>
                    <span>WhatsApp</span>
                </a>
            </div>
        </div>
    </div>
    <header class="sticky top-0 z-40 border-b bg-white" x-data="{catOpen:false}">
        <div class="mx-auto grid max-w-7xl grid-cols-12 items-center gap-3 px-4 py-3 md:min-h-[76px]">
            <div class="col-span-7 flex min-w-0 items-center gap-3 md:col-span-3">
                <a href="{{ route('home') }}" class="flex h-12 min-w-0 items-center" title="{{ $settings->site_name ?? 'Shoply' }}">
                    <span class="truncate text-2xl font-semibold tracking-tight leading-none md:text-3xl">nimtech.co.ke</span>
                </a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a class="hidden rounded bg-gray-100 px-2 py-1 text-xs md:inline-flex" href="{{ route('admin.dashboard') }}">Admin</a>
                    @endif
                @endauth
            </div>
            <div class="order-3 col-span-12 md:order-none md:col-span-4">
                <form action="{{ route('products.index') }}" method="GET" class="relative w-full">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..." class="h-11 w-full rounded-full border border-gray-300 bg-white pl-4 pr-11 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500" />
                    <button class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500" aria-label="Search"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10.5 3a7.5 7.5 0 105.236 12.764l3.75 3.75a.75.75 0 101.06-1.06l-3.75-3.75A7.5 7.5 0 0010.5 3zm-6 7.5a6 6 0 1110.91 3.546.75.75 0 00-.126.126A6 6 0 014.5 10.5z" clip-rule="evenodd" /></svg></button>
                </form>
            </div>
            <nav class="col-span-5 flex items-center justify-end gap-3 text-sm md:col-span-5">
                <a class="hover:underline" href="{{ route('seo.phones') }}">Phones</a>
                <a class="hover:underline" href="{{ route('seo.laptops') }}">Laptops</a>
                <a class="hover:underline" href="{{ route('deals.index') }}">Deals</a>
                <a class="hover:underline" href="{{ route('products.index') }}">Products</a>
                <a class="hover:underline" href="{{ route('brands.index') }}">Brands</a>
                @php($__cartCount = array_sum(\App\Support\Cart::all()))
                <a class="relative hover:underline flex items-center gap-1" href="{{ route('cart.index') }}">
                    <span>Cart</span>
                    @if($__cartCount > 0)
                      <span class="inline-flex items-center justify-center text-[10px] leading-none bg-red-600 text-white rounded-full w-4 h-4">{{ min($__cartCount, 99) }}</span>
                    @endif
                </a>
                @auth
                  <div class="relative" x-data="{open:false}" @keydown.escape.window="open=false" @click.outside="open=false">
                    <button @click="open=!open" class="px-2 py-1 border rounded inline-flex items-center gap-1" :aria-expanded="open ? 'true' : 'false'">
                      {{ auth()->user()->name }}
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"><path fill-rule="evenodd" d="M12 14.25a.75.75 0 01-.53-.22l-4.5-4.5a.75.75 0 111.06-1.06L12 12.44l3.97-3.97a.75.75 0 111.06 1.06l-4.5 4.5a.75.75 0 01-.53.22z" clip-rule="evenodd"/></svg>
                    </button>
                    <div
                      x-cloak
                      x-show="open"
                      x-transition.opacity.scale.origin-top-right
                      class="absolute right-0 mt-2 bg-white border rounded shadow text-sm w-40 z-50"
                    >
                       <a class="block px-3 py-2 hover:bg-gray-50" href="{{ route('dashboard') }}">Dashboard</a>
                       <form method="POST" action="{{ route('logout') }}">
                         @csrf
                         <button class="w-full text-left px-3 py-2 hover:bg-gray-50">Logout</button>
                       </form>
                     </div>
                   </div>
                @else
                  <a class="hover:underline" href="{{ route('login') }}">Login</a>
                @endauth
            </nav>
        </div>
    </header>
    @include('theme::partials.header-categories')

    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    @include('theme::partials.bottom-nav')
    @include('theme::partials.whatsapp-widget')
    @include('theme::partials.footer')
</body>
</html>
