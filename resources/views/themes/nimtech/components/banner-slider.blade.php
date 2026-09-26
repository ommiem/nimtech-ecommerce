@props(['slides' => collect()])
@php
  $jsSlides = $slides->map(fn($s) => [
    'title' => $s->title,
    'text' => $s->text,
    'cta' => $s->cta_text,
    'href' => $s->cta_url,
    'bg' => $s->bg ?: 'from-blue-50 to-indigo-50',
    'image' => $s->image ? image_src($s->image) : null,
  ]);
@endphp
@if($slides->count() > 0)
<section x-data="{ i: 0, slides: @js($jsSlides), timer: null, start(){ this.timer = setInterval(() => this.i = (this.i+1) % this.slides.length, 5000) }, stop(){ if(this.timer) clearInterval(this.timer) } }" @mouseenter="stop()" @mouseleave="start()" x-init="start()" class="overflow-hidden rounded border bg-white text-gray-950 shadow-sm">
  <template x-for="(s, idx) in slides" :key="idx">
    <div x-show="i === idx" x-transition.opacity class="bg-gradient-to-r text-gray-950" :class="s.bg">
      <div class="grid grid-cols-1 items-center gap-6 px-5 py-8 md:grid-cols-2 md:px-8 md:py-12">
        <div>
          <div class="mb-3 text-xs font-medium uppercase tracking-wide text-red-700">Featured offer</div>
          <h2 class="text-2xl font-semibold leading-tight text-gray-950 md:text-3xl" x-text="s.title"></h2>
          <p class="mt-3 text-sm leading-6 text-gray-700" x-text="s.text"></p>
          <div class="mt-4" x-show="s.cta && s.href">
            <a :href="s.href" class="inline-flex items-center justify-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700" x-text="s.cta"></a>
          </div>
        </div>
        <div class="hidden md:block">
          <template x-if="s.image">
            <img :src="s.image" :alt="s.title || 'Slide image'" class="h-44 w-full rounded border bg-white object-cover shadow-sm md:h-56">
          </template>
          <template x-if="!s.image">
            <div class="h-44 w-full rounded border bg-white/70 md:h-56"></div>
          </template>
        </div>
      </div>
      <div class="flex items-center justify-center gap-2 pb-4">
        <template x-for="(dot, d) in slides" :key="d">
          <button @click="i=d" :class="i===d ? 'bg-gray-800' : 'bg-gray-300'" class="h-2 w-2 rounded-full"></button>
        </template>
      </div>
    </div>
  </template>
</section>
@endif
