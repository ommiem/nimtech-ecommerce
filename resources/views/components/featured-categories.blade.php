@props(['categories'])
<section class="grid grid-cols-2 md:grid-cols-4 gap-4">
  @foreach($categories as $cat)
    @php($thumb = $cat->image_path ?: $cat->products()->whereNotNull('image')->latest('id')->value('image'))
    <a href="{{ route('categories.show', $cat->canonical_slug) }}" class="group bg-white border rounded overflow-hidden hover:shadow-sm">
      <div class="aspect-[4/3] w-full bg-gray-100 flex items-center justify-center">
        @if($thumb)
          <img src="{{ image_src($thumb) }}" alt="{{ $cat->name }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
        @else
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-8 h-8 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15m-15 4.5h15m-15 4.5h15"/></svg>
        @endif
      </div>
      <div class="p-3 text-center">
        <div class="font-medium group-hover:text-blue-700">{{ $cat->name }}</div>
        <div class="text-xs text-gray-500">{{ $cat->products()->count() }} items</div>
      </div>
    </a>
  @endforeach
</section>
