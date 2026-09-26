@props([
  'type' => 'info', // info, success, error, warning
])
@php
  $colors = [
    'info' => 'bg-blue-50 text-blue-800 border-blue-200',
    'success' => 'bg-green-50 text-green-800 border-green-200',
    'error' => 'bg-red-50 text-red-800 border-red-200',
    'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
  ];
  $classes = $colors[$type] ?? $colors['info'];
@endphp

<div x-data="{ open: true }" x-show="open" class="mb-4 border rounded p-3 {{ $classes }}">
  <div class="flex items-start justify-between gap-2">
    <div class="leading-relaxed">
      {{ $slot }}
    </div>
    <button type="button" class="text-sm opacity-70 hover:opacity-100" @click="open=false">✕</button>
  </div>
</div>

