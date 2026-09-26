@props(['items' => []])
@php
  $items = array_values(array_filter($items, fn ($item) => !empty($item['label'] ?? null)));
  $fallbackUrl = canonical_url($__env->yieldContent('canonical_url', request()->getRequestUri()));
  $schemaItems = [];

  foreach ($items as $index => $item) {
    $itemUrl = $item['url'] ?? null;
    if (empty($itemUrl) && $index === count($items) - 1) {
      $itemUrl = $fallbackUrl;
    }

    $schemaItems[] = array_filter([
      '@type' => 'ListItem',
      'position' => $index + 1,
      'name' => $item['label'],
      'item' => !empty($itemUrl) ? canonical_url($itemUrl) : null,
    ], fn ($value) => !is_null($value));
  }

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => $schemaItems,
  ];
@endphp
@if(count($schemaItems) > 1)
  <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
<nav class="text-sm text-gray-600 mb-4" aria-label="Breadcrumb">
  <ol class="flex items-center flex-wrap gap-1">
    @foreach($items as $i => $item)
      @if(!empty($item['url']) && $i < count($items) - 1)
        <li>
          <a href="{{ $item['url'] }}" class="hover:underline">{{ $item['label'] }}</a>
        </li>
        <li class="text-gray-400">/</li>
      @else
        <li class="font-semibold text-gray-800" aria-current="page">{{ $item['label'] }}</li>
      @endif
    @endforeach
  </ol>
</nav>


