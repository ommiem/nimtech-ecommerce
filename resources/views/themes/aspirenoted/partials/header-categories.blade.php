@php
  $settings = \App\Models\Setting::getCached();
  $headerCategories = \App\Models\Category::query()
      ->orderByDesc('is_featured')
      ->orderByRaw('COALESCE(sort_order, 999999) asc')
      ->orderBy('name')
      ->take(20)
      ->get();
  $storedSpecialTiles = json_decode((string) ($settings->header_special_tiles ?? '[]'), true);
  $storedSpecialTiles = is_array($storedSpecialTiles) ? $storedSpecialTiles : [];
  $specialTiles = !empty($storedSpecialTiles) ? $storedSpecialTiles : config('header.special_tiles', []);
  $specialTiles = array_slice($specialTiles, 0, 6);
@endphp
@if($headerCategories->count())
<div class="bg-white border-b">
  <div x-data="{scroll(n){ $refs.row.scrollBy({left:n, behavior:'smooth'}) }, showAll:false}" class="relative max-w-7xl mx-auto px-4 py-3">
    <button type="button" class="hidden md:flex absolute -left-2 top-1/2 -translate-y-1/2 z-10 bg-white border rounded-full w-8 h-8 items-center justify-center shadow" @click="scroll(-300)" aria-label="Scroll left">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
    </button>
    <div class="overflow-x-auto scroll-smooth no-scrollbar" x-ref="row">
      <div class="flex items-stretch gap-4 min-w-max">
        @foreach($specialTiles as $tile)
          @php($href = str_starts_with($tile['url'] ?? '#','http') ? $tile['url'] : url($tile['url'] ?? '#'))
          @php($tileImage = (string) ($tile['image'] ?? ''))
          @php($tileImageSrc = $tileImage !== '' ? (\Illuminate\Support\Str::startsWith($tileImage, ['http://', 'https://']) ? $tileImage : (\Illuminate\Support\Str::startsWith($tileImage, ['images/', 'storage/']) ? asset($tileImage) : asset('storage/'.$tileImage))) : null)
          <a href="{{ $href }}" class="w-24 sm:w-28 flex-shrink-0 text-center">
            <div class="mx-auto h-16 w-16 sm:h-18 sm:w-18 rounded-xl overflow-hidden {{ $tile['bg'] ?? 'bg-gray-900 text-white' }} flex items-center justify-center">
              @if($tileImageSrc)
                <img src="{{ $tileImageSrc }}" alt="{{ $tile['label'] ?? '' }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
              @else
                <span class="text-xs font-medium px-1">{{ $tile['label'] ?? '' }}</span>
              @endif
            </div>
            <div class="mt-1 text-[11px] sm:text-xs text-gray-800">{{ $tile['label'] ?? '' }}</div>
          </a>
        @endforeach
        @foreach($headerCategories as $cat)
          @php($thumb = $cat->image_path ?: $cat->products()->whereNotNull('image')->latest('id')->value('image'))
          <a href="{{ route('categories.show', $cat->canonical_slug) }}" class="w-24 sm:w-28 flex-shrink-0 text-center">
            <div class="mx-auto h-16 w-16 sm:h-18 sm:w-18 rounded-xl overflow-hidden bg-gray-100 border">
              @if($thumb)
                <img src="{{ image_src($thumb) }}" alt="{{ $cat->name }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
              @else
                <div class="h-full w-full flex items-center justify-center text-gray-400">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15m-15 4.5h15m-15 4.5h15"/></svg>
                </div>
              @endif
            </div>
            <div class="mt-1 text-[11px] sm:text-xs text-gray-800">{{ \Illuminate\Support\Str::limit($cat->name, 18) }}</div>
          </a>
        @endforeach
        <button type="button" @click="showAll=true" class="w-24 sm:w-28 flex-shrink-0 text-center">
          <div class="mx-auto h-16 w-16 sm:h-18 sm:w-18 rounded-xl overflow-hidden bg-white border flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-6 h-6 text-gray-600"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
          </div>
          <div class="mt-1 text-[11px] sm:text-xs text-gray-800">All</div>
        </button>
      </div>
    </div>
    <button type="button" class="hidden md:flex absolute -right-2 top-1/2 -translate-y-1/2 z-10 bg-white border rounded-full w-8 h-8 items-center justify-center shadow" @click="scroll(300)" aria-label="Scroll right">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </button>
  </div>
  <!-- All categories modal (teleported to body to escape sticky contexts) -->
  <template x-teleport="body">
    <div x-show="showAll" x-transition.opacity class="fixed inset-0 z-[999] overflow-y-auto" @keydown.escape.window="showAll=false">
      <div class="min-h-full flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/40" @click="showAll=false"></div>
        <div class="relative bg-white rounded shadow-xl w-[92vw] max-w-4xl p-4">
          <div class="flex items-center justify-between mb-3">
            <div class="font-semibold">All Categories</div>
            <button class="p-2" @click="showAll=false" aria-label="Close">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach(\App\Models\Category::orderByDesc('is_featured')->orderByRaw('COALESCE(sort_order,999999) asc')->orderBy('name')->get() as $cat)
              @php($thumb = $cat->image_path ?: $cat->products()->whereNotNull('image')->latest('id')->value('image'))
              <a href="{{ route('categories.show', $cat->canonical_slug) }}" class="group bg-white border rounded overflow-hidden hover:shadow-sm text-center">
                <div class="aspect-[4/3] w-full bg-gray-100 flex items-center justify-center">
                  @if($thumb)
                    <img src="{{ image_src($thumb) }}" alt="{{ $cat->name }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                  @else
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-8 h-8 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15m-15 4.5h15m-15 4.5h15"/></svg>
                  @endif
                </div>
                <div class="p-2 text-xs font-medium group-hover:text-blue-700">{{ $cat->name }}</div>
              </a>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </template>
</div>
@endif

