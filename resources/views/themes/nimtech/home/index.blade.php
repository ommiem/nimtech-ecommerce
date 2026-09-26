@extends('theme::layouts.app')

@php
  $settings = \App\Models\Setting::getCached();
  $siteName = data_get($settings, 'site_name', config('app.name', 'Shoply'));
  $homeUrl = canonical_url('/');
  $organizationSchema = array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    '@id' => $homeUrl.'#organization',
    'name' => $siteName,
    'url' => $homeUrl,
    'logo' => !empty($settings?->logo_path) ? asset('storage/'.$settings->logo_path) : null,
    'email' => data_get($settings, 'contact_email'),
    'telephone' => data_get($settings, 'contact_phone'),
    'address' => !empty(data_get($settings, 'contact_address'))
      ? [
          '@type' => 'PostalAddress',
          'streetAddress' => data_get($settings, 'contact_address'),
          'addressCountry' => 'KE',
        ]
      : null,
    'areaServed' => [
      '@type' => 'Country',
      'name' => 'Kenya',
    ],
  ], fn ($value) => !is_null($value));
  $websiteSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    '@id' => $homeUrl.'#website',
    'name' => $siteName,
    'url' => $homeUrl,
    'inLanguage' => 'en-KE',
    'potentialAction' => [
      '@type' => 'SearchAction',
      'target' => [
        '@type' => 'EntryPoint',
        'urlTemplate' => canonical_url('/products?q={search_term_string}'),
      ],
      'query-input' => 'required name=search_term_string',
    ],
  ];
@endphp
@section('meta_title', 'Phones, Laptops and Electronics in Kenya | '.$siteName)
@section('meta_description', 'Buy phones, laptops, TVs and electronics in Nairobi and across Kenya from '.$siteName.'. Competitive prices, warranty support and fast delivery.')
@section('canonical_url', '/')
@section('meta')
<script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection
@section('content')
  <section class="mt-4 overflow-hidden rounded border bg-white">
    <div class="grid gap-0 lg:grid-cols-[1.05fr_0.95fr]">
      <div class="px-4 py-7 sm:px-7 lg:px-8 lg:py-9">
        <div class="inline-flex items-center rounded border border-red-100 bg-red-50 px-3 py-1 text-xs font-medium text-red-700">
          Nairobi electronics store
        </div>
        <h1 class="mt-4 max-w-3xl text-3xl font-semibold leading-tight text-gray-950 sm:text-4xl lg:text-5xl">
          Phones, Laptops and Electronics in Kenya
        </h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-gray-600 sm:text-base">
          Compare live prices, warranty-backed devices, and curated tech deals from Nimtech. Shop online or visit our Nairobi CBD store for practical buying help.
        </p>

        <form action="{{ route('products.index') }}" method="GET" class="mt-5 flex flex-col gap-2 sm:flex-row">
          <label class="sr-only" for="home-search">Search products</label>
          <div class="relative flex-1">
            <input id="home-search" type="text" name="q" value="{{ request('q') }}" placeholder="Search iPhone, HP laptop, earbuds..." class="w-full rounded border border-gray-300 px-4 py-3 pr-11 text-base focus:border-blue-600 focus:ring-2 focus:ring-blue-500" />
            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path fill-rule="evenodd" d="M10.5 3a7.5 7.5 0 105.236 12.764l3.75 3.75a.75.75 0 101.06-1.06l-3.75-3.75A7.5 7.5 0 0010.5 3zm-6 7.5a6 6 0 1110.91 3.546.75.75 0 00-.126.126A6 6 0 014.5 10.5z" clip-rule="evenodd" /></svg>
            </span>
          </div>
          <button class="inline-flex items-center justify-center rounded bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Search</button>
        </form>

        <div class="mt-5 flex flex-wrap gap-2 text-sm">
          <a href="{{ route('seo.phones') }}" class="rounded border border-gray-200 px-3 py-2 font-medium hover:border-red-200 hover:bg-red-50">Phones</a>
          <a href="{{ route('seo.laptops') }}" class="rounded border border-gray-200 px-3 py-2 font-medium hover:border-red-200 hover:bg-red-50">Laptops</a>
          <a href="{{ route('deals.index') }}" class="rounded border border-gray-200 px-3 py-2 font-medium hover:border-red-200 hover:bg-red-50">Deals</a>
          <a href="{{ route('products.index') }}" class="rounded border border-gray-200 px-3 py-2 font-medium hover:border-red-200 hover:bg-red-50">All products</a>
        </div>

        <div class="mt-6 grid grid-cols-3 gap-3 border-t pt-5 text-sm">
          <div>
            <div class="text-lg font-semibold text-gray-950">{{ ($newProducts ?? collect())->count() }}+</div>
            <div class="text-xs text-gray-500">New picks</div>
          </div>
          <div>
            <div class="text-lg font-semibold text-gray-950">{{ ($popularProducts ?? collect())->count() }}+</div>
            <div class="text-xs text-gray-500">Popular items</div>
          </div>
          <div>
            <div class="text-lg font-semibold text-gray-950">KES</div>
            <div class="text-xs text-gray-500">Local pricing</div>
          </div>
        </div>
      </div>

      <div class="border-t bg-gray-100 p-4 lg:border-l lg:border-t-0 lg:p-6">
        <x-banner-slider :slides="$slides" />
      </div>
    </div>
  </section>

  <section class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-3">
    <div class="rounded border bg-white p-4">
      <div class="flex items-start gap-3">
        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded bg-red-50 text-blue-600">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
        </span>
        <div>
          <div class="font-semibold text-gray-950">Original tech selection</div>
          <div class="mt-1 text-sm leading-5 text-gray-600">Phones, laptops, TVs, accessories, repairs, and office devices.</div>
        </div>
      </div>
    </div>
    <div class="rounded border bg-white p-4">
      <div class="flex items-start gap-3">
        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded bg-red-50 text-blue-600">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </span>
        <div>
          <div class="font-semibold text-gray-950">Fast Nairobi support</div>
          <div class="mt-1 text-sm leading-5 text-gray-600">Order online, ask on WhatsApp, or visit near National Archives.</div>
        </div>
      </div>
    </div>
    <div class="rounded border bg-white p-4">
      <div class="flex items-start gap-3">
        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded bg-red-50 text-blue-600">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </span>
        <div>
          <div class="font-semibold text-gray-950">Warranty guidance</div>
          <div class="mt-1 text-sm leading-5 text-gray-600">Clear specs, stock checks, and practical buyer recommendations.</div>
        </div>
      </div>
    </div>
  </section>

  <section class="mt-6 rounded border bg-white p-4 sm:p-5">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="text-xl font-semibold text-gray-950">Shop high-intent categories</h2>
        <p class="mt-1 text-sm text-gray-600">Shortcuts for common Kenya electronics searches.</p>
      </div>
      <a href="{{ route('products.index') }}" class="text-sm font-medium text-blue-700 hover:underline">Browse catalog</a>
    </div>
    <div class="mt-4 grid grid-cols-2 gap-3 text-sm md:grid-cols-4">
      <a href="{{ route('seo.phones') }}" class="rounded border bg-gray-50 px-3 py-3 font-medium hover:bg-white">Phones in Kenya</a>
      <a href="{{ route('seo.laptops') }}" class="rounded border bg-gray-50 px-3 py-3 font-medium hover:bg-white">Laptops in Kenya</a>
      <a href="{{ route('seo.iphone-kenya') }}" class="rounded border bg-gray-50 px-3 py-3 font-medium hover:bg-white">iPhone in Kenya</a>
      <a href="{{ route('seo.samsung-kenya') }}" class="rounded border bg-gray-50 px-3 py-3 font-medium hover:bg-white">Samsung Phones Kenya</a>
      <a href="{{ route('seo.hp-laptops-kenya') }}" class="rounded border bg-gray-50 px-3 py-3 font-medium hover:bg-white">HP Laptops Kenya</a>
      <a href="{{ route('seo.dell-laptops-kenya') }}" class="rounded border bg-gray-50 px-3 py-3 font-medium hover:bg-white">Dell Laptops Kenya</a>
      <a href="{{ route('seo.lenovo-laptops-kenya') }}" class="rounded border bg-gray-50 px-3 py-3 font-medium hover:bg-white">Lenovo Laptops Kenya</a>
      <a href="{{ route('seo.xiaomi-phones-kenya') }}" class="rounded border bg-gray-50 px-3 py-3 font-medium hover:bg-white">Xiaomi Phones Kenya</a>
    </div>
  </section>

  @include('partials.home-campaigns')

  <!-- Product highlights -->
  <section class="mt-8">
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-xl font-semibold">Top picks for you</h2>
      <a href="{{ route('products.index') }}" class="group inline-flex items-center gap-1 text-sm text-blue-700 hover:underline">
        <span>Browse all products</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-4 h-4 transition-transform group-hover:translate-x-0.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
      </a>
    </div>
    @if($newProducts->count())
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
        @foreach($newProducts as $product)
          <x-product-card :product="$product" />
        @endforeach
      </div>
    @else
      <div class="bg-white border rounded p-6 text-gray-600">No products yet.</div>
    @endif
  </section>

  @if(($popularProducts ?? collect())->count())
  <section class="mt-10">
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-xl font-semibold">Popular now</h2>
      <a href="{{ route('products.index') }}" class="text-sm text-blue-700 hover:underline">Shop all</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
      @foreach($popularProducts as $product)
        <x-product-card :product="$product" />
      @endforeach
    </div>
  </section>
  @endif

  @if(($showDeals ?? false) && ($budgetProducts ?? collect())->count())
  <section class="mt-10">
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-xl font-semibold">Top deals under {{ currency_format($dealsThreshold) }}</h2>
      <a href="{{ route('products.index', ['price_max' => $dealsThreshold]) }}" class="text-sm text-blue-700 hover:underline">View more</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
      @foreach($budgetProducts as $product)
        <x-product-card :product="$product" />
      @endforeach
    </div>
  </section>
  @endif

  <section class="mt-12 bg-white border rounded p-5 sm:p-7">
    <div class="max-w-none">
      <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight">Phones and Laptops in Kenya: Complete Nimtech Buying Guide</h2>
      <p class="mt-3 text-sm sm:text-base text-gray-700">
        This guide is designed for shoppers in Kenya who want practical advice before buying a phone, laptop, or accessory online. At Nimtech, we focus on helping you choose the right device for your real use case, not just the most advertised option. Whether you are buying a phone for business communication, a laptop for campus work, or a performance machine for editing and gaming, you can use this page as a decision framework. You can move directly into our dedicated authority pages for <a href="{{ route('seo.phones') }}" class="text-blue-700 hover:underline">Phones in Kenya</a> and <a href="{{ route('seo.laptops') }}" class="text-blue-700 hover:underline">Laptops in Kenya</a>, then compare live listings inside <a href="{{ route('products.index') }}" class="text-blue-700 hover:underline">our product catalog</a>.
      </p>

      <div class="mt-5 grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
        <a href="{{ route('seo.phones') }}" class="rounded border bg-gray-50 px-3 py-2 hover:bg-white">Phones in Kenya</a>
        <a href="{{ route('seo.laptops') }}" class="rounded border bg-gray-50 px-3 py-2 hover:bg-white">Laptops in Kenya</a>
        <a href="{{ route('seo.iphone-kenya') }}" class="rounded border bg-gray-50 px-3 py-2 hover:bg-white">iPhone in Kenya</a>
        <a href="{{ route('seo.samsung-kenya') }}" class="rounded border bg-gray-50 px-3 py-2 hover:bg-white">Samsung Phones Kenya</a>
        <a href="{{ route('seo.hp-laptops-kenya') }}" class="rounded border bg-gray-50 px-3 py-2 hover:bg-white">HP Laptops Kenya</a>
        <a href="{{ route('seo.dell-laptops-kenya') }}" class="rounded border bg-gray-50 px-3 py-2 hover:bg-white">Dell Laptops Kenya</a>
        <a href="{{ route('seo.lenovo-laptops-kenya') }}" class="rounded border bg-gray-50 px-3 py-2 hover:bg-white">Lenovo Laptops Kenya</a>
        <a href="{{ route('seo.xiaomi-phones-kenya') }}" class="rounded border bg-gray-50 px-3 py-2 hover:bg-white">Xiaomi Phones Kenya</a>
      </div>

      <div class="mt-8 space-y-3">
        <details class="group rounded border bg-gray-50" open>
          <summary class="cursor-pointer list-none px-4 py-3 font-semibold text-lg flex items-center justify-between">
            <span>How to buy the right phone in Kenya</span>
            <span class="text-xs text-gray-500 group-open:hidden">Expand</span>
            <span class="text-xs text-gray-500 hidden group-open:inline">Collapse</span>
          </summary>
          <div class="px-4 pb-4 space-y-3">
            <p class="text-sm sm:text-base text-gray-700">
              A smart phone purchase should begin with your daily habits. If most of your time is spent on WhatsApp, social media, mobile banking, and video calls, you need stable performance, enough RAM, and dependable battery life more than flashy specifications. For many buyers, a balanced device with at least 6GB RAM and 128GB storage offers strong day-to-day value. If you record content regularly, focus on camera consistency and stabilization rather than just megapixel numbers. Our <a href="{{ route('seo.phones') }}" class="text-blue-700 hover:underline">phones guide</a> explains this in practical terms and links to real products so you can compare based on budget, network support, and long term usability.
            </p>
            <p class="text-sm sm:text-base text-gray-700">
              Kenyan buyers also need to think about longevity before checkout. A lower upfront price can become expensive if the phone slows down quickly, lacks software updates, or requires frequent charging in heavy use. The best value often comes from choosing a model that remains smooth over a longer period. Compare processor class, storage speed, and battery optimization when deciding. You can also review pricing patterns through <a href="{{ route('deals.index') }}" class="text-blue-700 hover:underline">our deals page</a> and then move to product detail pages for complete specs, pricing in KES, and availability. This helps you avoid impulse buying and match your device to real workload needs.
            </p>
          </div>
        </details>

        <details class="group rounded border bg-gray-50">
          <summary class="cursor-pointer list-none px-4 py-3 font-semibold text-lg flex items-center justify-between">
            <span>iPhone, Samsung, and Xiaomi buying strategy</span>
            <span class="text-xs text-gray-500 group-open:hidden">Expand</span>
            <span class="text-xs text-gray-500 hidden group-open:inline">Collapse</span>
          </summary>
          <div class="px-4 pb-4 space-y-3">
            <p class="text-sm sm:text-base text-gray-700">
              If you are deciding among the most searched smartphone brands in Kenya, your ecosystem and usage style should drive the final choice. iPhone users usually value software consistency, camera reliability, and long update cycles, which is why our <a href="{{ route('seo.iphone-kenya') }}" class="text-blue-700 hover:underline">iPhone in Kenya page</a> focuses on model comparison, storage options, and practical pricing guidance. Samsung users often want flexibility across budget, mid range, and premium options, and our <a href="{{ route('seo.samsung-kenya') }}" class="text-blue-700 hover:underline">Samsung phones page</a> helps map each buyer profile to suitable model tiers without overcomplicating the process.
            </p>
            <p class="text-sm sm:text-base text-gray-700">
              Xiaomi remains a strong value brand for shoppers who prioritize specification to price ratio, especially in the entry and mid range segments. If that fits your budget strategy, start from our <a href="{{ route('seo.xiaomi-phones-kenya') }}" class="text-blue-700 hover:underline">Xiaomi phones Kenya page</a> and then compare memory options, display size, battery behavior, and build quality against alternatives in the same range. Internal comparison is important because two phones at similar prices can differ significantly in long term smoothness. We recommend narrowing your shortlist to three models and then reviewing live stock and pricing from the <a href="{{ route('products.index') }}" class="text-blue-700 hover:underline">product listing page</a>.
            </p>
          </div>
        </details>

        <details class="group rounded border bg-gray-50">
          <summary class="cursor-pointer list-none px-4 py-3 font-semibold text-lg flex items-center justify-between">
            <span>Laptop buying guide for students, work, and performance users</span>
            <span class="text-xs text-gray-500 group-open:hidden">Expand</span>
            <span class="text-xs text-gray-500 hidden group-open:inline">Collapse</span>
          </summary>
          <div class="px-4 pb-4 space-y-3">
            <p class="text-sm sm:text-base text-gray-700">
              Laptop selection in Kenya should begin with your primary workload. Student buyers usually need portability, battery life, and enough RAM for browser-based learning, assignments, and research tools. Office and business users typically need stable multitasking, clear webcam quality, comfortable keyboards, and strong day-long reliability. Creator and gaming users need higher CPU and GPU capability with proper cooling. Our <a href="{{ route('seo.laptops') }}" class="text-blue-700 hover:underline">laptops in Kenya page</a> breaks these needs into practical segments so you can quickly identify what matters most and avoid paying for features that do not improve your daily workflow.
            </p>
            <p class="text-sm sm:text-base text-gray-700">
              RAM, SSD, and processor choices are the biggest decision points for most buyers. As a practical baseline, 8GB RAM with SSD storage works for everyday use, but buyers handling heavier multitasking, advanced spreadsheets, design tools, or coding will often benefit from 16GB RAM. Processor generation also matters because newer chip families often provide better efficiency and smoother sustained performance. We encourage shoppers to compare with a life cycle mindset, not just launch marketing. When in doubt, choose the configuration that keeps your system responsive for the next few years. You can then check current options and pricing directly on <a href="{{ route('products.index') }}" class="text-blue-700 hover:underline">our catalog</a>.
            </p>
          </div>
        </details>

        <details class="group rounded border bg-gray-50">
          <summary class="cursor-pointer list-none px-4 py-3 font-semibold text-lg flex items-center justify-between">
            <span>Choosing between HP, Dell, and Lenovo laptops in Kenya</span>
            <span class="text-xs text-gray-500 group-open:hidden">Expand</span>
            <span class="text-xs text-gray-500 hidden group-open:inline">Collapse</span>
          </summary>
          <div class="px-4 pb-4 space-y-3">
            <p class="text-sm sm:text-base text-gray-700">
              HP, Dell, and Lenovo remain the most common laptop searches for buyers in Nairobi and across Kenya, but each brand has different strengths depending on model family and budget. HP is often selected for broad availability across student, office, and performance tiers. Dell is widely preferred by professionals who prioritize reliability and durable business-focused lines. Lenovo is a strong pick for balanced value, keyboard comfort, and productivity consistency. If you want cleaner comparisons before committing, use our brand authority pages: <a href="{{ route('seo.hp-laptops-kenya') }}" class="text-blue-700 hover:underline">HP laptops Kenya</a>, <a href="{{ route('seo.dell-laptops-kenya') }}" class="text-blue-700 hover:underline">Dell laptops Kenya</a>, and <a href="{{ route('seo.lenovo-laptops-kenya') }}" class="text-blue-700 hover:underline">Lenovo laptops Kenya</a>.
            </p>
            <p class="text-sm sm:text-base text-gray-700">
              A reliable comparison method is to shortlist machines by use case first, then compare build quality, port selection, thermal design, and upgrade flexibility. For example, if your role depends on long writing sessions and stable multitasking, keyboard ergonomics and sustained performance are more important than cosmetic design. If your workload includes editing or heavier creative tasks, graphics performance and cooling behavior should be weighted higher. Brand labels help, but the exact model configuration matters more than the logo. We recommend reviewing both brand pages and the complete <a href="{{ route('brands.index') }}" class="text-blue-700 hover:underline">shop by brand directory</a> before making final decisions.
            </p>
          </div>
        </details>

        <details class="group rounded border bg-gray-50">
          <summary class="cursor-pointer list-none px-4 py-3 font-semibold text-lg flex items-center justify-between">
            <span>Budget planning, price tracking, and deal hunting</span>
            <span class="text-xs text-gray-500 group-open:hidden">Expand</span>
            <span class="text-xs text-gray-500 hidden group-open:inline">Collapse</span>
          </summary>
          <div class="px-4 pb-4 space-y-3">
            <p class="text-sm sm:text-base text-gray-700">
              Strong buying decisions are usually made before checkout, when you define a clear budget ceiling and separate essential features from optional features. This is especially useful for first-time buyers, students, and small business owners trying to control spending. Instead of starting with the most expensive listings, begin by setting a realistic range and filtering products based on your exact need. You can use the category links above, then check value-oriented selections under <a href="{{ route('deals.index') }}" class="text-blue-700 hover:underline">current deals</a>. Price-sensitive buyers can also compare our affordable and premium segments on the authority pages to understand where extra spending creates real performance benefit.
            </p>
            <p class="text-sm sm:text-base text-gray-700">
              For phone buyers under tight budgets, a practical strategy is to prioritize battery, storage, and stable chipset behavior first, then camera extras second. For laptop buyers, prioritize SSD, RAM, and processor class before cosmetic upgrades. This prevents underperforming purchases and reduces replacement pressure after a short period. As you compare options, always check stock status and specific configuration details to avoid mismatches. Once you are ready, add preferred products to <a href="{{ route('cart.index') }}" class="text-blue-700 hover:underline">your cart</a> and review totals clearly before checkout. This step-by-step process keeps the purchase efficient, transparent, and aligned with your long term use.
            </p>
          </div>
        </details>

        <details class="group rounded border bg-gray-50">
          <summary class="cursor-pointer list-none px-4 py-3 font-semibold text-lg flex items-center justify-between">
            <span>Warranty, delivery, and after-sale confidence in Kenya</span>
            <span class="text-xs text-gray-500 group-open:hidden">Expand</span>
            <span class="text-xs text-gray-500 hidden group-open:inline">Collapse</span>
          </summary>
          <div class="px-4 pb-4 space-y-3">
            <p class="text-sm sm:text-base text-gray-700">
              Trust in online electronics shopping is built through clear communication, realistic expectations, and support after payment. At Nimtech, our goal is to provide transparent product information, practical recommendations, and straightforward purchase flow so customers can buy confidently. Buyers should always review warranty context, available support channels, and delivery scope before placing orders. This approach protects your budget and reduces post-purchase uncertainty. If you are unsure what to buy, start from the authority links on this homepage, then move to individual product pages for exact specifications and pricing details. We also encourage buyers to compare similar models side by side to ensure the final selection fits the intended workload.
            </p>
            <p class="text-sm sm:text-base text-gray-700">
              Delivery planning matters as much as product selection, especially for buyers outside major urban centers. Confirm your preferred destination details early, use accurate contact information, and keep your order references available for smoother support. After sale support should not be an afterthought; it should be part of your decision process before checkout. A good purchase experience includes guidance, not just payment confirmation. For this reason, we maintain internal resource pages and curated brand hubs so customers can make informed decisions quickly. Use the route links throughout this guide to continue deeper into each segment and choose the right product category with confidence.
            </p>
          </div>
        </details>

        <details class="group rounded border bg-gray-50">
          <summary class="cursor-pointer list-none px-4 py-3 font-semibold text-lg flex items-center justify-between">
            <span>Internal navigation map: the fastest path to your product</span>
            <span class="text-xs text-gray-500 group-open:hidden">Expand</span>
            <span class="text-xs text-gray-500 hidden group-open:inline">Collapse</span>
          </summary>
          <div class="px-4 pb-4 space-y-3">
            <p class="text-sm sm:text-base text-gray-700">
              If you want the fastest route through the website, begin with one of the high intent authority pages, then drill down to live product listings and finalize based on budget. For smartphones, start at <a href="{{ route('seo.phones') }}" class="text-blue-700 hover:underline">Phones in Kenya</a>, then continue to <a href="{{ route('seo.iphone-kenya') }}" class="text-blue-700 hover:underline">iPhone</a>, <a href="{{ route('seo.samsung-kenya') }}" class="text-blue-700 hover:underline">Samsung</a>, or <a href="{{ route('seo.xiaomi-phones-kenya') }}" class="text-blue-700 hover:underline">Xiaomi</a> depending on your brand preference. For laptops, start at <a href="{{ route('seo.laptops') }}" class="text-blue-700 hover:underline">Laptops in Kenya</a> and branch into <a href="{{ route('seo.hp-laptops-kenya') }}" class="text-blue-700 hover:underline">HP</a>, <a href="{{ route('seo.dell-laptops-kenya') }}" class="text-blue-700 hover:underline">Dell</a>, or <a href="{{ route('seo.lenovo-laptops-kenya') }}" class="text-blue-700 hover:underline">Lenovo</a>.
            </p>
            <p class="text-sm sm:text-base text-gray-700">
              After reviewing your preferred guide, move to <a href="{{ route('products.index') }}" class="text-blue-700 hover:underline">all products</a> to compare current stock and pricing, then check <a href="{{ route('deals.index') }}" class="text-blue-700 hover:underline">deals</a> for budget opportunities. If you prefer browsing by manufacturer first, use <a href="{{ route('brands.index') }}" class="text-blue-700 hover:underline">shop by brand</a>. Once your shortlist is ready, add items to <a href="{{ route('cart.index') }}" class="text-blue-700 hover:underline">cart</a> and proceed with a clear view of totals. This navigation flow reduces guesswork, saves time, and improves conversion quality because every click is tied to a specific buying intention.
            </p>
          </div>
        </details>

        <details class="group rounded border bg-gray-50">
          <summary class="cursor-pointer list-none px-4 py-3 font-semibold text-lg flex items-center justify-between">
            <span>Category discovery for additional product types</span>
            <span class="text-xs text-gray-500 group-open:hidden">Expand</span>
            <span class="text-xs text-gray-500 hidden group-open:inline">Collapse</span>
          </summary>
          <div class="px-4 pb-4 space-y-3">
            <p class="text-sm sm:text-base text-gray-700">
              In addition to phones and laptops, many shoppers explore accessories, office devices, audio products, and home technology from the same storefront. If you are building a complete setup, you can combine your primary device purchase with practical add-ons such as wireless peripherals, charging accessories, audio equipment, and productivity tools. This helps avoid multiple separate purchases and keeps compatibility decisions in one flow. Use category pages to narrow this journey quickly, then return to product-level comparison for exact fit. Good accessory choices can improve the long term value of your phone or laptop purchase by enhancing convenience, safety, and day-to-day usability.
            </p>
            <p class="text-sm sm:text-base text-gray-700">
              @if(($categories ?? collect())->count())
                You can explore common category paths directly:
                @foreach(($categories ?? collect())->take(8) as $cat)
                  <a href="{{ route('categories.show', $cat->canonical_slug) }}" class="text-blue-700 hover:underline">{{ $cat->name }}</a>@if(!$loop->last), @endif
                @endforeach
                . Category-level browsing is useful when you are still in research mode and need to see price distribution before narrowing to a specific model. Once you identify a suitable range, shift to the authority pages and brand pages above to make a confident final selection with less trial and error.
              @else
                Category-level browsing is useful when you are still in research mode and need to see price distribution before narrowing to a specific model. Once you identify a suitable range, shift to the authority pages and brand pages above to make a confident final selection with less trial and error.
              @endif
            </p>
          </div>
        </details>

        <details class="group rounded border bg-gray-50">
          <summary class="cursor-pointer list-none px-4 py-3 font-semibold text-lg flex items-center justify-between">
            <span>Common search intent in Kenya and how this site answers it</span>
            <span class="text-xs text-gray-500 group-open:hidden">Expand</span>
            <span class="text-xs text-gray-500 hidden group-open:inline">Collapse</span>
          </summary>
          <div class="px-4 pb-4 space-y-3">
            <p class="text-sm sm:text-base text-gray-700">
              Many shoppers arrive with highly specific search intent such as phone price in Kenya, best laptop for students, affordable gaming laptop in Nairobi, or where to buy original smartphones online. This homepage section is structured to answer those intents with clear next steps instead of generic claims. If your goal is pricing and model comparison, start with <a href="{{ route('products.index') }}" class="text-blue-700 hover:underline">products</a>. If your goal is educational context before purchase, use the dedicated authority pages linked above. If your goal is brand-first exploration, use <a href="{{ route('brands.index') }}" class="text-blue-700 hover:underline">brand listings</a> and then move to individual product pages for exact specification and stock details.
            </p>
            <p class="text-sm sm:text-base text-gray-700">
              Search engines also prioritize strong topic coverage and internal relevance, which is why this guide intentionally connects phones, laptops, brands, categories, and deals into one coherent navigation map. Users benefit because every path is practical and conversion-focused. Crawlers benefit because the relationship between pages is explicit, improving discoverability of high-value internal URLs such as <a href="{{ route('seo.phones') }}" class="text-blue-700 hover:underline">phones in Kenya</a>, <a href="{{ route('seo.laptops') }}" class="text-blue-700 hover:underline">laptops in Kenya</a>, and brand-focused hubs. This combined approach strengthens authority over time while helping first-time visitors quickly reach the exact product segment they need.
            </p>
          </div>
        </details>

        <details class="group rounded border bg-gray-50">
          <summary class="cursor-pointer list-none px-4 py-3 font-semibold text-lg flex items-center justify-between">
            <span>Final recommendation for serious buyers in Kenya</span>
            <span class="text-xs text-gray-500 group-open:hidden">Expand</span>
            <span class="text-xs text-gray-500 hidden group-open:inline">Collapse</span>
          </summary>
          <div class="px-4 pb-4 space-y-3">
            <p class="text-sm sm:text-base text-gray-700">
              The most reliable way to buy electronics online is to match product capability to real workload, compare three practical alternatives, and choose the option that offers strong long term performance within your budget. Avoid making decisions based only on trends, and focus on what improves your daily experience. Use this homepage SEO guide as your anchor, then continue through the internal links provided to move from research to action without friction. Nimtech is structured to support that exact path: informative authority pages, clear category navigation, transparent product cards, and direct checkout flow.
            </p>
            <p class="text-sm sm:text-base text-gray-700">
              Start now with <a href="{{ route('seo.phones') }}" class="text-blue-700 hover:underline">Phones in Kenya</a> or <a href="{{ route('seo.laptops') }}" class="text-blue-700 hover:underline">Laptops in Kenya</a>, compare options on <a href="{{ route('products.index') }}" class="text-blue-700 hover:underline">the products page</a>, and monitor savings through <a href="{{ route('deals.index') }}" class="text-blue-700 hover:underline">deals</a>. If you already know your preferred manufacturer, jump directly into <a href="{{ route('brands.index') }}" class="text-blue-700 hover:underline">shop by brand</a>. This internal journey gives both users and search engines a clear content map, strengthens topical authority for Kenyan electronics queries, and supports long term growth in visibility and conversion quality.
            </p>
          </div>
        </details>
      </div>
    </div>
  </section>
@endsection

