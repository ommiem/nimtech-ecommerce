@php($settings = \App\Models\Setting::getCached())
<footer class="border-t bg-white text-sm text-gray-700">
  <div class="bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row items-center gap-3 md:gap-6 justify-between">
      <div class="text-center md:text-left">
        <div class="text-lg md:text-xl font-semibold">{{ $settings->homepage_cta_heading ?? 'Fresh bodycare and fragrance arrivals' }}</div>
        <div class="text-xs md:text-sm text-white/80">{{ $settings->homepage_cta_subtext ?? 'Shop daily care essentials with Kenya delivery and WhatsApp support.' }}</div>
      </div>
      <div class="flex w-full md:w-auto justify-center md:justify-end gap-2">
        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-white text-blue-700 rounded font-medium">Shop Now</a>
        <a href="{{ whatsapp_link('Hello Aspire Noted, I need help choosing a product') }}" class="px-4 py-2 border border-white text-white rounded">WhatsApp</a>
      </div>
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
    <div>
      <div class="font-semibold mb-2">{{ $settings->site_name ?? 'Aspire Noted' }}</div>
      <p class="text-gray-600">Bodycare, fragrances, lotions, deodorants and daily care essentials in Kenya.</p>
    </div>

    <div>
      <div class="font-semibold mb-2">Shop</div>
      <ul class="space-y-1 text-gray-600">
        <li><a class="hover:underline" href="{{ route('products.index') }}">All Products</a></li>
        <li><a class="hover:underline" href="{{ route('deals.index') }}">Deals</a></li>
        <li><a class="hover:underline" href="{{ route('brands.index') }}">Brands</a></li>
        <li><a class="hover:underline" href="{{ route('cart.index') }}">Your Cart</a></li>
      </ul>
    </div>

    <div>
      <div class="font-semibold mb-2">Account</div>
      <ul class="space-y-1 text-gray-600">
        @auth
          <li><a class="hover:underline" href="{{ route('dashboard') }}">Dashboard</a></li>
          <li><a class="hover:underline" href="{{ route('account.orders.index') }}">My Orders</a></li>
        @else
          <li><a class="hover:underline" href="{{ route('login') }}">Login</a></li>
          <li><a class="hover:underline" href="{{ route('register') }}">Register</a></li>
        @endauth
      </ul>
    </div>

    <div>
      <div class="font-semibold mb-2">Support</div>
      <p class="text-gray-600 mb-3">Ask about product fit, delivery, pickup, or order support before you buy.</p>
      <a href="{{ whatsapp_link('Hello Aspire Noted, I need support with my order') }}" class="inline-flex px-4 py-2 bg-green-600 text-white rounded font-medium">Chat on WhatsApp</a>
    </div>
  </div>

  <div class="border-t">
    <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between text-xs text-gray-500">
      <div>&copy; {{ date('Y') }} {{ $settings->site_name ?? 'Aspire Noted' }}. All rights reserved.</div>
      <div class="space-x-3">
        <a class="hover:underline" href="{{ route('products.index') }}">Products</a>
        <a class="hover:underline" href="{{ route('deals.index') }}">Deals</a>
        <a class="hover:underline" href="{{ whatsapp_link('Hello Aspire Noted, I need support') }}">Contact</a>
      </div>
    </div>
  </div>
</footer>
