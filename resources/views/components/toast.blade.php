@props(['message' => session('success') ?? session('error') ?? null, 'type' => session('success') ? 'success' : (session('error') ? 'error' : 'info')])
<div x-data="{ show: {{ $message ? 'true' : 'false' }}, hide(){ this.show=false }, message: @js($message), type: '{{ $type }}' }" x-show="show" x-transition class="fixed bottom-4 right-4 z-50">
  <div :class="{'bg-green-600': type==='success', 'bg-red-600': type==='error', 'bg-gray-800': type==='info'}" class="text-white px-4 py-3 rounded shadow flex items-start gap-3">
    <div x-text="message"></div>
    <button class="opacity-80" @click="hide()">✕</button>
  </div>
</div>

