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
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
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
    <script src="https://analytics.ahrefs.com/analytics.js" data-key="u2ZxmcB5OL+usxwN/WCNDA" async></script>
    @include('partials.tracking-head')
</head>
<body class="bg-gray-50 text-gray-900">
    @include('partials.tracking-body')
    <div class="bg-blue-600 text-white text-sm">
        <div class="max-w-7xl mx-auto px-4 py-2 text-center">Free delivery for orders over {{ currency_format(5000) }} â€¢ New arrivals every week!</div>
    </div>
    <header class="bg-white border-b sticky top-0 z-40" x-data="{catOpen:false}">
        <div class="max-w-7xl mx-auto px-4 py-3 grid grid-cols-12 gap-4 items-center">
            <div class="col-span-12 md:col-span-3 flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-3 py-1 md:py-2" title="{{ $settings->site_name ?? 'Shoply' }}">
                    @if(!empty($settings->logo_path))
                      <img src="{{ asset('storage/'.$settings->logo_path) }}" alt="{{ $settings->site_name ?? 'Shoply' }}" class="h-12 md:h-16 w-auto object-contain drop-shadow-sm">
                    @else
                      <span class="text-3xl md:text-4xl font-semibold tracking-tight leading-none">{{ $settings->site_name ?? 'Shoply' }}</span>
                    @endif
                </a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a class="text-xs px-2 py-1 rounded bg-gray-100" href="{{ route('admin.dashboard') }}">Admin</a>
                    @endif
                @endauth
            </div>
            <div class="col-span-12 md:col-span-6">
                <form action="{{ route('products.index') }}" method="GET" class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..." class="w-full border rounded-full pl-4 pr-10 py-2 focus:ring-2 focus:ring-blue-500" />
                    <button class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500" aria-label="Search"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10.5 3a7.5 7.5 0 105.236 12.764l3.75 3.75a.75.75 0 101.06-1.06l-3.75-3.75A7.5 7.5 0 0010.5 3zm-6 7.5a6 6 0 1110.91 3.546.75.75 0 00-.126.126A6 6 0 014.5 10.5z" clip-rule="evenodd" /></svg></button>
                </form>
            </div>
            <nav class="col-span-12 md:col-span-3 flex justify-end items-center gap-4 text-sm">
                <a class="hover:underline" href="{{ route('deals.index') }}">Deals</a>
                <a class="hover:underline" href="{{ route('products.index') }}">Products</a>
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
    @include('partials.header-categories')

    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    @include('partials.bottom-nav')
    @include('partials.whatsapp-widget')

    <footer class="border-t bg-white text-sm text-gray-700">
        <!-- Footer Call To Action -->
        <div class="bg-blue-600 text-white">
            <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row items-center gap-3 md:gap-6 justify-between">
                <div class="text-center md:text-left">
                    <div class="text-lg md:text-xl font-semibold">{{ $settings->homepage_cta_heading ?? 'Donâ€™t miss new arrivals and offers' }}</div>
                    <div class="text-xs md:text-sm text-white/80">{{ $settings->homepage_cta_subtext ?? 'Shop the latest products and enjoy fast delivery.' }}</div>
                </div>
                <div class="flex w-full md:w-auto justify-center md:justify-end gap-2">
                    <a href="{{ route('products.index') }}" class="px-4 py-2 bg-white text-blue-700 rounded font-medium">Shop Now</a>
                    <a href="{{ whatsapp_link('Hello! I have a question about your products') }}" class="px-4 py-2 border border-white text-white rounded">WhatsApp</a>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
            <div>
                <div class="font-semibold mb-2">{{ $settings->site_name ?? config('app.name', 'Shoply') }}</div>
                @if($settings)
                    <div class="text-gray-600">{{ $settings->contact_address }}</div>
                    <div class="mt-1">Phone: {{ $settings->contact_phone }}</div>
                    <div class="mt-1">Email: <a class="hover:underline" href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a></div>
                @endif
            </div>
            <div>
                <div class="font-semibold mb-2">Help</div>
                <ul class="space-y-1 text-gray-600">
                    <li><a class="hover:underline" href="{{ route('products.index') }}">Browse Products</a></li>
                    <li><a class="hover:underline" href="{{ route('cart.index') }}">Your Cart</a></li>
                    <li><a class="hover:underline" href="#">Shipping & Returns</a></li>
                    <li><a class="hover:underline" href="#">FAQ</a></li>
                </ul>
            </div>
            <div>
                <div class="font-semibold mb-2">Account</div>
                <ul class="space-y-1 text-gray-600">
                    @auth
                        <li><a class="hover:underline" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li><a class="hover:underline" href="{{ route('account.orders.index') }}">My Orders</a></li>
                        <li><a class="hover:underline" href="{{ route('account.profile') }}">Profile</a></li>
                    @else
                        <li><a class="hover:underline" href="{{ route('login') }}">Login</a></li>
                        <li><a class="hover:underline" href="{{ route('register') }}">Register</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <div class="font-semibold mb-2">Newsletter</div>
                <p class="text-gray-600 mb-2">Get updates on new arrivals and offers.</p>
                <form class="flex gap-2" onsubmit="return false;">
                    <input type="email" placeholder="Your email" class="border rounded px-3 py-2 flex-1"/>
                    <button type="button" class="px-3 py-2 bg-blue-600 text-white rounded">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="border-t">
            <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between text-xs text-gray-500">
                <div>&copy; {{ date('Y') }} {{ $settings->site_name ?? config('app.name', 'Shoply') }}. All rights reserved.</div>
                <div class="space-x-3">
                    <a class="hover:underline" href="#">Privacy</a>
                    <a class="hover:underline" href="#">Terms</a>
                    <a class="hover:underline" href="mailto:{{ $settings->contact_email ?? 'support@example.com' }}">Contact</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>





