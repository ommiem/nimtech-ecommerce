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
<section x-data="{ i: 0, slides: @js($jsSlides), timer: null, start(){ this.timer = setInterval(() => this.i = (this.i+1) % this.slides.length, 5000) }, stop(){ if(this.timer) clearInterval(this.timer) } }" @mouseenter="stop()" @mouseleave="start()" x-init="start()" class="rounded-xl overflow-hidden border">
  <template x-for="(s, idx) in slides" :key="idx">
    <div x-show="i === idx" x-transition.opacity class="bg-gradient-to-r" :class="s.bg">
      <div class="px-6 py-10 md:px-10 md:py-16 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
        <div>
          <h2 class="text-3xl md:text-4xl font-semibold" x-text="s.title"></h2>
          <p class="mt-2 text-gray-700" x-text="s.text"></p>
          <div class="mt-4" x-show="s.cta && s.href">
            <a :href="s.href" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" x-text="s.cta"></a>
          </div>
        </div>
        <div class="hidden md:block">
          <template x-if="s.image">
            <img :src="s.image" :alt="s.title || 'Slide image'" class="h-40 md:h-56 w-full object-cover border rounded-xl">
          </template>
          <template x-if="!s.image">
            <div class="h-40 md:h-56 w-full bg-white/60 border rounded-xl"></div>
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
