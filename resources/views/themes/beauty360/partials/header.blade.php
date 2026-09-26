@php($settings = \App\Models\Setting::getCached())
@php($topNotice = trim((string) ($settings->header_notice_text ?? '')))
<div class="bg-blue-600 text-white text-sm">
    <div class="max-w-7xl mx-auto px-4 py-2 text-center">{{ $topNotice !== '' ? $topNotice : ('Free delivery for orders over '.currency_format(5000).' - New arrivals every week!') }}</div>
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

