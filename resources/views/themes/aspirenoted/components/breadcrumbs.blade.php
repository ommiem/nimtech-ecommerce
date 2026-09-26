@props(['items' => []])
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


