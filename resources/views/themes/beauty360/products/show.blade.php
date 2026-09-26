@extends('theme::layouts.app')

@php
  $siteName = data_get(\App\Models\Setting::getCached(), 'site_name', config('app.name', 'Shoply'));
  $productMetaDescription = Str::limit(strip_tags($product->description ?? ''), 150);
@endphp
@section('meta_title', $product->name.' | '.$siteName)
@section('meta_description', $productMetaDescription !== '' ? $productMetaDescription : ('Buy '.$product->name.' at '.$siteName.'. Fast delivery across Kenya.'))
@section('canonical_url', route('products.show', $product))

@section('meta')
<meta property="og:type" content="product">
@if($product->image)<meta property="og:image" content="{{ asset('storage/'.$product->image) }}">@endif
    {{-- highlights prepared in controller --}}
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endsection
@section('content')
<x-breadcrumbs :items="[
  ['label' => 'Home', 'url' => route('home')],
  ['label' => $product->category?->name, 'url' => route('categories.show', $product->category?->canonical_slug)],
  ['label' => $product->name]
]" />

@php($waOrderBase = whatsapp_link("Hello, I'm interested in {$product->name} (".url()->current().")"))
@php($waOrderConfig = [
  'baseUrl' => $waOrderBase,
  'productId' => $product->id,
  'productName' => $product->name,
  'productUrl' => url()->current(),
  'storeUrl' => route('whatsapp.leads.store'),
  'csrf' => csrf_token(),
])
<div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
  <div x-data="{
        current: 0,
        zoom:false,
        lightbox:false,
        startX:0,
        onStart(e){ this.startX = (e.touches? e.touches[0].clientX : e.clientX) },
        onEnd(e){ const x = (e.changedTouches? e.changedTouches[0].clientX : e.clientX); const d = x - this.startX; if(Math.abs(d) > 40){ if(d < 0 && this.current < (@js(count($imagePaths))-1)) this.current++; if(d > 0 && this.current > 0) this.current--; } }
      }" @keydown.escape.window="lightbox=false">
    <a href="{{ route('products.index') }}" class="text-sm text-gray-600 hover:underline">&larr; Back to products</a>
    <div class="mt-3 bg-white border rounded p-2">
      @if(count($imagePaths))
        <div class="lg:flex lg:gap-3">
          <!-- Thumbs left on desktop -->
          <div class="hidden lg:flex lg:flex-col lg:w-24 gap-2">
            @foreach($imagePaths as $i=>$img)
              <button
                type="button"
                class="relative border rounded overflow-hidden focus:outline-none"
                @click="current={{ $i }}"
                :class="{ 'ring-2 ring-blue-500 border-blue-500': current === {{ $i }} }"
                aria-label="Thumbnail {{ $i+1 }}"
              >
                <img src="{{ image_src($img) }}" class="h-20 w-full object-cover" loading="lazy" alt="Thumbnail {{ $i+1 }}">
              </button>
            @endforeach
          </div>
          <!-- Main image -->
          <div class="relative overflow-hidden flex-1" @touchstart="onStart($event)" @touchend="onEnd($event)">
            <img :src="'{{ asset('storage') }}/' + @js($imagePaths)[current]" alt="{{ $product->name }}" class="w-full max-h-[28rem] object-cover rounded transition-transform duration-150 cursor-zoom-in" :class="{'scale-110': zoom}" @mouseenter="zoom=true" @mouseleave="zoom=false" @click="lightbox=true" decoding="async">
          </div>
        </div>

        <!-- Thumbs bottom on mobile -->
        <div class="mt-3 flex gap-2 overflow-x-auto no-scrollbar lg:hidden snap-x">
          @foreach($imagePaths as $i=>$img)
            <button
              type="button"
              class="relative flex-shrink-0 border rounded overflow-hidden focus:outline-none h-16 w-20 snap-start"
              @click="current={{ $i }}"
              :class="{ 'ring-2 ring-blue-500 border-blue-500': current === {{ $i }} }"
              aria-label="Thumbnail {{ $i+1 }}"
            >
              <img src="{{ image_src($img) }}" class="h-full w-full object-cover" loading="lazy" alt="Thumbnail {{ $i+1 }}">
            </button>
          @endforeach
        </div>
      @else
        <div class="h-80 w-full bg-gray-100 flex items-center justify-center text-gray-400">No Image</div>
      @endif
    </div>
    <!-- Lightbox -->
    <div x-show="lightbox" x-transition class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center" @click.self="lightbox=false">
      <img :src="'{{ asset('storage') }}/' + @js($imagePaths)[current]" class="max-h-[90vh] max-w-[90vw] rounded" alt="{{ $product->name }} large image">
      <button class="absolute top-4 right-4 text-white text-2xl" @click="lightbox=false" aria-label="Close">&times;</button>
    </div>
  </div>

  <div>
    @auth
      @if(method_exists(auth()->user(),'isAdmin') && auth()->user()->isAdmin())
        <div class="flex justify-end mb-2">
          <a href="{{ route('admin.products.edit', $product) }}" class="px-3 py-1.5 border rounded text-sm hover:bg-gray-50">Edit</a>
        </div>
      @endif
    @endauth
    <h1 class="text-3xl font-semibold">{{ $product->name }}</h1>
    <div class="mt-1 text-sm text-gray-500">
      @if($product->category)
        <a href="{{ route('categories.show', $product->category->canonical_slug) }}" class="hover:underline">{{ $product->category->name }}</a>
      @endif
    </div>

    <div class="mt-3 flex items-center gap-4">
      <div class="text-3xl font-bold">{{ currency_format($product->price) }}</div>
      <div class="flex items-center gap-2 text-yellow-500" title="Rating">
        @for($i=1; $i<=5; $i++)
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.036a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118L10.5 13.347a1 1 0 00-1.175 0l-2.985 2.136c-.784.57-1.838-.197-1.54-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.703 8.72c-.783-.57-.38-1.81.588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z"/></svg>
        @endfor
        <span class="text-xs text-gray-500">(Rated)</span>
      </div>
      <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs {{ $product->stock > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
        {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
      </span>
    </div>

    {{-- Desktop actions: Add to Cart + Buy Now + WhatsApp --}}
    <div class="mt-4 hidden md:block">
      <div class="rounded-xl border bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
          <form action="{{ route('cart.add', [], false) }}" method="POST" class="flex flex-1 flex-col sm:flex-row sm:items-center gap-2">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="flex items-center gap-2">
              <label class="text-sm text-gray-600" for="qty-{{ $product->id }}">Qty</label>
              <input id="qty-{{ $product->id }}" name="quantity" type="number" min="1" @if($product->stock>0) max="{{ (int) $product->stock }}" @endif value="1" class="w-20 border rounded-lg px-2 py-1.5">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg sm:flex-1 {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock <= 0 ? 'disabled' : '' }}>Add to Cart</button>
            <button type="submit" formaction="{{ route('cart.add', ['redirect' => 'checkout'], false) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg sm:flex-1 {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock <= 0 ? 'disabled' : '' }}>Buy Now</button>
          </form>
          <a href="{{ $waOrderBase }}" target="_blank" rel="noopener" data-wa-order-open class="inline-flex items-center justify-center px-4 py-2 border border-green-600 text-green-700 rounded-lg font-medium hover:bg-green-50 whitespace-nowrap" aria-haspopup="dialog">Order via WhatsApp</a>
        </div>
        <div class="mt-2 text-xs text-gray-500">Fast response on WhatsApp. We’ll include your number in the message.</div>
      </div>
    </div>

    @if($product->description)
      <div class="mt-4 prose max-w-none">{!! $product->description !!}</div>
    @endif
    {{-- highlights prepared in controller --}}
    <div class="mt-6 divide-y rounded border bg-white">
      <details class="group p-4" open>
        <summary class="flex justify-between items-center cursor-pointer list-none">
          <span class="font-medium">Details</span>
          <span class="transition-transform group-open:rotate-180 text-gray-500" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
          </span>
        </summary>
        <div class="mt-3 text-sm text-gray-700">
          @if($product->description)
            {!! $product->description !!}
          @else
            <em>No additional details.</em>
          @endif
        </div>
      </details>
      <details class="group p-4">
        <summary class="flex justify-between items-center cursor-pointer list-none">
          <span class="font-medium">Highlights</span>
          <span class="transition-transform group-open:rotate-180 text-gray-500" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
          </span>
        </summary>
        <ul class="mt-3 text-sm text-gray-700 list-disc pl-5 space-y-1">
          @foreach(array_slice(($highlights ?? []),0,6) as $h)
            <li>{{ $h }}</li>
          @endforeach
        </ul>
      </details>
      <details class="group p-4">
        <summary class="flex justify-between items-center cursor-pointer list-none">
          <span class="font-medium">Delivery & Returns</span>
          <span class="transition-transform group-open:rotate-180 text-gray-500" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
          </span>
        </summary>
        <div class="mt-3 text-sm text-gray-700">
          Enjoy fast delivery across Kenya. Returns accepted within 7 days for unopened items. Contact us for assistance.
        </div>
      </details>
    </div>
  </div>
</div>

@if(!empty($related) && $related->count())
  <div x-data="{scroll(n){ $refs.rel.scrollBy({left:n, behavior:'smooth'}) }}" class="mt-12 relative">
    <h2 class="text-xl font-semibold mb-4">You may also like</h2>
    <div class="relative">
      <button type="button" class="hidden md:flex absolute -left-3 top-1/2 -translate-y-1/2 z-10 bg-white border rounded-full w-10 h-10 items-center justify-center shadow" @click="scroll(-300)" aria-label="Scroll left">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
      </button>
      <div class="overflow-x-auto scroll-smooth no-scrollbar snap-x" x-ref="rel">
        <div class="flex gap-4 min-w-max">
          @foreach($related as $rp)
            <div class="snap-start w-56">
              <x-product-card :product="$rp" />
            </div>
          @endforeach
        </div>
      </div>
      <button type="button" class="hidden md:flex absolute -right-3 top-1/2 -translate-y-1/2 z-10 bg-white border rounded-full w-10 h-10 items-center justify-center shadow" @click="scroll(300)" aria-label="Scroll right">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
      </button>
    </div>
  </div>
@endif

<!-- Sticky Add to Cart (mobile) -->
<div class="fixed md:hidden bottom-14 inset-x-0 bg-white border-t shadow-lg p-3 z-50">
  <div class="flex items-center gap-3">
    <div class="font-semibold">{{ currency_format($product->price) }}</div>
    <form action="{{ route('cart.add', [], false) }}" method="POST" class="ml-auto flex items-center gap-2">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="product_id" value="{{ $product->id }}">
      <input type="hidden" name="quantity" value="1">
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock <= 0 ? 'disabled' : '' }}>Add to Cart</button>
      <button type="submit" formaction="{{ route('cart.add', ['redirect' => 'checkout'], false) }}" class="px-4 py-2 bg-green-600 text-white rounded {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock <= 0 ? 'disabled' : '' }}>Buy Now</button>
      <a href="{{ $waOrderBase }}" target="_blank" rel="noopener" data-wa-order-open class="px-3 py-2 border border-green-600 text-green-700 rounded text-xs sm:text-sm whitespace-nowrap" aria-haspopup="dialog">Order via WhatsApp</a>
    </form>
  </div>
</div>

<!-- Order via WhatsApp Modal -->
<div
  id="wa-order-modal"
  data-wa-order-modal
  class="hidden fixed inset-0 z-[60] items-center justify-center bg-black/50 p-4"
  role="dialog"
  aria-modal="true"
  aria-labelledby="wa-order-title"
>
  <div class="w-full max-w-sm rounded-lg bg-white p-5 shadow-lg">
    <div class="flex items-start justify-between gap-3">
      <h3 id="wa-order-title" class="text-lg font-semibold">Order via WhatsApp</h3>
      <button type="button" class="text-gray-400 hover:text-gray-600" data-wa-order-close aria-label="Close">&times;</button>
    </div>
    <p class="mt-1 text-sm text-gray-600">We will include it in your order message.</p>
    <form class="mt-4" data-wa-order-form>
      <label class="text-sm text-gray-700" for="wa-number">WhatsApp number</label>
      <input
        id="wa-number"
        data-wa-order-input
        type="tel"
        inputmode="tel"
        autocomplete="tel"
        placeholder="+254 712 345 678"
        class="mt-1 w-full rounded border px-3 py-2 focus:ring-2 focus:ring-green-500"
      >
      <p class="mt-1 text-xs text-gray-500">Include country code if possible.</p>
      <p class="mt-2 text-xs text-red-600 hidden" data-wa-order-error></p>
      <div class="mt-4 flex gap-2">
        <button type="button" class="flex-1 rounded border px-3 py-2 text-sm" data-wa-order-close>Cancel</button>
        <button type="submit" class="flex-1 rounded bg-green-600 px-3 py-2 text-sm text-white">Continue to WhatsApp</button>
      </div>
    </form>
  </div>
</div>

<script>
  (function () {
    var config = @js($waOrderConfig);

    function storeLead(phone, message) {
      if (!config.storeUrl) return;
      var form = new FormData();
      form.append("_token", config.csrf || "");
      form.append("phone", phone);
      if (config.productId) form.append("product_id", String(config.productId));
      if (config.productName) form.append("product_name", config.productName);
      if (config.productUrl) form.append("product_url", config.productUrl);
      if (message) form.append("message", message);

      if (navigator.sendBeacon) {
        try {
          if (navigator.sendBeacon(config.storeUrl, form)) return;
        } catch (e) {}
      }

      try {
        fetch(config.storeUrl, {
          method: "POST",
          credentials: "same-origin",
          headers: {
            "X-CSRF-TOKEN": config.csrf || "",
            "X-Requested-With": "XMLHttpRequest"
          },
          body: form,
          keepalive: true
        });
      } catch (e) {}
    }

    function buildWhatsAppUrl(raw) {
      var url = new URL(config.baseUrl, window.location.origin);
      var baseText = url.searchParams.get("text") || "";
      var extra = "Customer WhatsApp: " + raw;
      var text = baseText ? (baseText + "\n" + extra) : extra;
      url.searchParams.set("text", text);
      return { url: url.toString(), message: text };
    }

    document.addEventListener("DOMContentLoaded", function () {
      var modal = document.querySelector("[data-wa-order-modal]");
      if (!modal) return;

      var input = modal.querySelector("[data-wa-order-input]");
      var errorEl = modal.querySelector("[data-wa-order-error]");
      var form = modal.querySelector("[data-wa-order-form]");
      var openButtons = document.querySelectorAll("[data-wa-order-open]");
      var closeButtons = modal.querySelectorAll("[data-wa-order-close]");

      function showError(msg) {
        if (!errorEl) return;
        errorEl.textContent = msg;
        errorEl.classList.remove("hidden");
      }

      function clearError() {
        if (!errorEl) return;
        errorEl.textContent = "";
        errorEl.classList.add("hidden");
      }

      function openModal() {
        clearError();
        modal.classList.remove("hidden");
        modal.style.display = "flex";
        try {
          var saved = localStorage.getItem("waCustomerPhone");
          if (saved && input) input.value = saved;
        } catch (e) {}
        if (input) input.focus();
      }

      function closeModal() {
        modal.classList.add("hidden");
        modal.style.display = "none";
      }

      openButtons.forEach(function (btn) {
        btn.addEventListener("click", function (e) {
          e.preventDefault();
          openModal();
        });
      });

      closeButtons.forEach(function (btn) {
        btn.addEventListener("click", function () {
          closeModal();
        });
      });

      modal.addEventListener("click", function (e) {
        if (e.target === modal) closeModal();
      });

      document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") closeModal();
      });

      if (form) {
        form.addEventListener("submit", function (e) {
          e.preventDefault();
          clearError();
          var raw = (input && input.value ? input.value : "").trim();
          var digits = raw.replace(/\D+/g, "");
          if (digits.length < 9) {
            showError("Please enter a valid WhatsApp number.");
            return;
          }
          try {
            localStorage.setItem("waCustomerPhone", raw);
          } catch (e) {}
          var result = buildWhatsAppUrl(raw);
          storeLead(raw, result.message);
          closeModal();
          var win = window.open(result.url, "_blank", "noopener");
          if (!win) {
            window.location.href = result.url;
          }
        });
      }
    });
  })();
</script>
</div>
@endsection


