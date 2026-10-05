@php($settings = \App\Models\Setting::getCached())
<footer class="border-t border-red-700 bg-slate-950 text-sm text-slate-300">
  <div class="bg-red-600 text-white">
    <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row items-center gap-3 md:gap-6 justify-between">
      <div class="text-center md:text-left">
        <div class="text-lg md:text-xl font-semibold">{{ $settings->homepage_cta_heading ?? 'Phones, Laptops and Electronics in Kenya' }}</div>
        <div class="text-xs md:text-sm text-white/80">{{ $settings->homepage_cta_subtext ?? 'Shop the latest products and enjoy fast delivery.' }}</div>
      </div>
      <div class="flex w-full md:w-auto justify-center md:justify-end gap-2">
        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-white text-red-700 rounded font-medium">Shop Now</a>
        <a href="{{ whatsapp_link('Hello! I have a question about your products') }}" class="px-4 py-2 border border-white text-white rounded">WhatsApp</a>
      </div>
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10">
    <div>
      <div class="font-semibold mb-2 text-white">{{ $settings->site_name ?? config('app.name', 'Shoply') }}</div>
      @if($settings)
        <div class="text-slate-400">{{ $settings->contact_address }}</div>
        <div class="mt-2 text-slate-400">Phone: {{ $settings->contact_phone }}</div>
        <div class="mt-1 text-slate-400">Email: <a class="text-slate-200 hover:text-white hover:underline" href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a></div>
      @endif
    </div>

    <div>
      <div class="font-semibold mb-2 text-white">Shop</div>
      <ul class="space-y-1.5 text-slate-400">
        <li><a class="hover:text-white hover:underline" href="{{ route('products.index') }}">All Products</a></li>
        <li><a class="hover:text-white hover:underline" href="{{ route('seo.phones') }}">Phones</a></li>
        <li><a class="hover:text-white hover:underline" href="{{ route('seo.laptops') }}">Laptops</a></li>
        <li><a class="hover:text-white hover:underline" href="{{ route('brands.index') }}">Shop by Brand</a></li>
        <li><a class="hover:text-white hover:underline" href="{{ route('seo.updated') }}">Latest Price Updates</a></li>
        <li><a class="hover:text-white hover:underline" href="{{ route('seo.compare') }}">Compare Products</a></li>
        <li><a class="hover:text-white hover:underline" href="{{ route('cart.index') }}">Your Cart</a></li>
      </ul>
    </div>

    <div>
      <div class="font-semibold mb-2 text-white">Buying Guides</div>
      <ul class="space-y-1.5 text-slate-400">
        <li><a class="hover:text-white hover:underline" href="{{ route('seo.catalog', 'phone-prices-in-kenya') }}">Live Phone Prices</a></li>
        <li><a class="hover:text-white hover:underline" href="{{ route('seo.catalog', 'laptop-prices-in-kenya') }}">Live Laptop Prices</a></li>
        <li><a class="hover:text-white hover:underline" href="{{ route('seo.iphone-kenya') }}">iPhone in Kenya</a></li>
        <li><a class="hover:text-white hover:underline" href="{{ route('seo.samsung-kenya') }}">Samsung Phones Kenya</a></li>
        <li><a class="hover:text-white hover:underline" href="{{ route('seo.hp-laptops-kenya') }}">HP Laptops Kenya</a></li>
        <li><a class="hover:text-white hover:underline" href="{{ route('seo.dell-laptops-kenya') }}">Dell Laptops Kenya</a></li>
        <li><a class="hover:text-white hover:underline" href="{{ route('seo.lenovo-laptops-kenya') }}">Lenovo Laptops Kenya</a></li>
        <li><a class="hover:text-white hover:underline" href="{{ route('seo.xiaomi-phones-kenya') }}">Xiaomi Phones Kenya</a></li>
      </ul>
    </div>

    <div>
      <div class="font-semibold mb-2 text-white">Account</div>
      <ul class="space-y-1.5 text-slate-400">
        @auth
          <li><a class="hover:text-white hover:underline" href="{{ route('dashboard') }}">Dashboard</a></li>
          <li><a class="hover:text-white hover:underline" href="{{ route('account.orders.index') }}">My Orders</a></li>
          <li><a class="hover:text-white hover:underline" href="{{ route('account.profile') }}">Profile</a></li>
        @else
          <li><a class="hover:text-white hover:underline" href="{{ route('login') }}">Login</a></li>
          <li><a class="hover:text-white hover:underline" href="{{ route('register') }}">Register</a></li>
        @endauth
      </ul>
    </div>

  </div>

  <div class="border-t border-slate-800 bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between text-xs text-slate-500">
      <div>&copy; {{ date('Y') }} {{ $settings->site_name ?? config('app.name', 'Shoply') }}. All rights reserved.</div>
      <div class="space-x-3">
        <a class="hover:text-white hover:underline" href="{{ route('pages.show', ['page' => 'privacy']) }}">Privacy</a>
        <a class="hover:text-white hover:underline" href="{{ route('pages.show', ['page' => 'terms']) }}">Terms</a>
        <a class="hover:text-white hover:underline" href="{{ route('pages.show', ['page' => 'contact']) }}">Contact</a>
      </div>
    </div>
  </div>
</footer>
