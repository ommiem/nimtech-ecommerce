@php
  $cartCount = array_sum(\App\Support\Cart::all());
  $isProducts = request()->routeIs('products.*');
  $isDeals = request()->routeIs('deals.index');
@endphp

<nav class="fixed bottom-0 inset-x-0 border-t bg-white shadow-lg z-40 md:hidden h-14">
  <div class="max-w-7xl mx-auto h-full flex text-[11px] text-gray-500">
    <a href="{{ route('home') }}" class="flex-1 flex flex-col items-center justify-center {{ request()->routeIs('home') ? 'text-blue-600' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 mb-0.5">
        <path d="M11.47 2.72a.75.75 0 0 1 1.06 0l8.25 8.25a.75.75 0 1 1-1.06 1.06L12 4.31 4.28 12.03a.75.75 0 0 1-1.06-1.06l8.25-8.25Z" />
        <path d="M12 5.56 18.97 12.53a.75.75 0 0 1 .22.53v6.19a.75.75 0 0 1-.75.75h-4.5a.75.75 0 0 1-.75-.75v-4.5h-3v4.5a.75.75 0 0 1-.75.75h-4.5a.75.75 0 0 1-.75-.75v-6.19a.75.75 0 0 1 .22-.53L12 5.56Z" />
      </svg>
      <span>Home</span>
    </a>

    <a href="{{ route('deals.index') }}" class="flex-1 flex flex-col items-center justify-center {{ $isDeals ? 'text-blue-600' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 mb-0.5">
        <path d="M4.5 5.25A2.25 2.25 0 0 1 6.75 3h3.879a2.25 2.25 0 0 1 1.59.659l8.122 8.121a2.25 2.25 0 0 1 0 3.182l-3.879 3.879a2.25 2.25 0 0 1-3.182 0l-8.12-8.122a2.25 2.25 0 0 1-.66-1.59V5.25Z" />
        <path d="M6.75 6a.75.75 0 1 0 0 1.5A.75.75 0 0 0 6.75 6Z" />
      </svg>
      <span>Deals</span>
    </a>

    <a href="{{ route('products.index') }}" class="flex-1 flex flex-col items-center justify-center {{ $isProducts ? 'text-blue-600' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 mb-0.5">
        <path d="M3 5.25C3 4.007 4.007 3 5.25 3h4.5C10.993 3 12 4.007 12 5.25v4.5C12 10.993 10.993 12 9.75 12h-4.5A2.25 2.25 0 0 1 3 9.75v-4.5Z" />
        <path d="M12 14.25C12 13.007 13.007 12 14.25 12h4.5C19.493 12 20.5 13.007 20.5 14.25v4.5A2.25 2.25 0 0 1 18.75 21h-4.5A2.25 2.25 0 0 1 12 18.75v-4.5Z" />
        <path d="M12 5.25C12 4.007 13.007 3 14.25 3h4.5C19.493 3 20.5 4.007 20.5 5.25v4.5C20.5 10.993 19.493 12 18.75 12h-4.5A2.25 2.25 0 0 1 12 9.75v-4.5Z" />
        <path d="M3 14.25C3 13.007 4.007 12 5.25 12h4.5C10.993 12 12 13.007 12 14.25v4.5A2.25 2.25 0 0 1 9.75 21h-4.5A2.25 2.25 0 0 1 3 18.75v-4.5Z" />
      </svg>
      <span>Products</span>
    </a>

    <a href="{{ route('cart.index') }}" class="relative flex-1 flex flex-col items-center justify-center {{ request()->routeIs('cart.*') ? 'text-blue-600' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 mb-0.5">
        <path d="M2.25 3a.75.75 0 0 1 .75-.75h1.386a1.5 1.5 0 0 1 1.415 1.028L6.47 6h13.28a.75.75 0 0 1 .732.919l-1.5 6A.75.75 0 0 1 18.25 13.5H7.31l.375 1.5h10.565a.75.75 0 0 1 0 1.5H7.125a1.5 1.5 0 0 1-1.447-1.086L3.22 3.79A.75.75 0 0 1 2.25 3Z" />
        <path d="M9 19.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm9 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
      </svg>
      <span>Cart</span>
      @if($cartCount > 0)
        <span class="absolute top-1 right-5 inline-flex items-center justify-center text-[10px] leading-none bg-red-600 text-white rounded-full min-w-[1.1rem] h-4 px-1">
          {{ min($cartCount, 99) }}
        </span>
      @endif
    </a>

    @auth
      @php($accountActive = request()->is('account*') || request()->routeIs('dashboard'))
      <a href="{{ route('account.orders.index') }}" class="flex-1 flex flex-col items-center justify-center {{ $accountActive ? 'text-blue-600' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 mb-0.5">
          <path d="M12 2.25a4.5 4.5 0 0 0-4.5 4.5v.75a4.5 4.5 0 0 0 9 0v-.75A4.5 4.5 0 0 0 12 2.25Z" />
          <path d="M4.5 20.25a7.5 7.5 0 0 1 15 0 .75.75 0 0 1-.75.75h-13.5a.75.75 0 0 1-.75-.75Z" />
        </svg>
        <span>Account</span>
      </a>
    @else
      <a href="{{ route('login') }}" class="flex-1 flex flex-col items-center justify-center {{ request()->routeIs('login') ? 'text-blue-600' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 mb-0.5">
          <path d="M15.75 3a3 3 0 0 1 3 3v3.75a.75.75 0 0 1-1.5 0V6a1.5 1.5 0 0 0-1.5-1.5h-9A1.5 1.5 0 0 0 5.25 6v12A1.5 1.5 0 0 0 6.75 19.5h9A1.5 1.5 0 0 0 17.25 18v-3.75a.75.75 0 0 1 1.5 0V18a3 3 0 0 1-3 3h-9a3 3 0 0 1-3-3V6a3 3 0 0 1 3-3h9Z" />
          <path d="M11.47 8.47a.75.75 0 0 1 1.06 0l2.25 2.25a.75.75 0 0 1 0 1.06l-2.25 2.25a.75.75 0 0 1-1.06-1.06L12.69 12l-1.22-1.22a.75.75 0 0 1 0-1.06Z" />
        </svg>
        <span>Login</span>
      </a>
    @endauth
  </div>
</nav>

