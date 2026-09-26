@if(($campaigns ?? collect())->count())
  <section class="mt-8">
    <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="text-xl font-semibold text-gray-950">Featured campaigns</h2>
        <p class="text-sm text-gray-600">Curated bundles and buyer-focused collections built around live Nimtech stock.</p>
      </div>
      <a href="{{ route('products.index') }}" class="text-sm font-medium text-blue-700 hover:underline">View all products</a>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
      @foreach($campaigns as $campaign)
        <article class="group overflow-hidden rounded border bg-white transition hover:-translate-y-0.5 hover:border-red-200 hover:shadow-md">
          @if($campaign->featured_image)
            <a href="{{ route('campaigns.show', $campaign) }}" class="block">
              <img src="{{ asset('storage/'.$campaign->featured_image) }}" alt="{{ $campaign->title }}" class="h-44 w-full object-cover">
            </a>
          @else
            <a href="{{ route('campaigns.show', $campaign) }}" class="block bg-gray-950 px-5 py-6 text-white">
              @if($campaign->hero_badge)
                <div class="text-xs uppercase text-gray-300">{{ $campaign->hero_badge }}</div>
              @endif
              <div class="mt-3 text-lg font-semibold leading-tight">{{ $campaign->title }}</div>
              <div class="mt-2 text-sm text-gray-200">{{ $campaign->products_count }} curated items</div>
            </a>
          @endif

          <div class="p-5">
            @if($campaign->hero_badge && !$campaign->featured_image)
              <div class="mb-2 text-xs font-medium uppercase tracking-wide text-blue-700">{{ $campaign->hero_badge }}</div>
            @elseif($campaign->hero_badge)
              <div class="mb-2 text-xs font-medium uppercase tracking-wide text-blue-700">{{ $campaign->hero_badge }}</div>
            @endif

            <h3 class="text-lg font-semibold leading-tight">
              <a href="{{ route('campaigns.show', $campaign) }}" class="hover:underline">{{ $campaign->title }}</a>
            </h3>

            <p class="mt-2 text-sm text-gray-600">
              {{ \Illuminate\Support\Str::limit($campaign->summary ?: $campaign->meta_description ?: 'Explore this curated Nimtech campaign built around live stock and practical buyer needs.', 140) }}
            </p>

            <div class="mt-4 flex items-center justify-between gap-3 text-sm">
              <span class="text-gray-500">{{ $campaign->products_count }} items</span>
              <a href="{{ route('campaigns.show', $campaign) }}" class="inline-flex items-center gap-1 font-medium text-blue-700 hover:underline">
                <span>View campaign</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
              </a>
            </div>
          </div>
        </article>
      @endforeach
    </div>
  </section>
@endif
