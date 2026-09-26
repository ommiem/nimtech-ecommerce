@php
  $whatsAppUrl = whatsapp_link('Hello! I have a question about your products');
@endphp

<div
  x-data="{ open: false }"
  class="fixed bottom-20 right-4 md:bottom-6 md:right-6 z-50"
  aria-live="polite"
>
  {{-- Chat panel --}}
  <div
    x-cloak
    x-show="open"
    x-transition.opacity.scale.origin-bottom-right
    class="mb-3 w-72 max-w-[85vw] bg-white border border-green-600/30 shadow-xl rounded-2xl overflow-hidden text-sm"
  >
    <div class="bg-green-600 text-white px-3 py-2 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/10">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75 0 1.74.458 3.37 1.26 4.78L2.25 21.75l5.07-1.23A9.708 9.708 0 0012 22.5c5.385 0 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-4.06 6.56a.75.75 0 01.845-.184l1.864.776a.75.75 0 01.41.995l-.38.952a.75.75 0 00.108.73c.45.62 1.057 1.226 1.676 1.676a.75.75 0 00.73.108l.952-.38a.75.75 0 01.995.41l.776 1.864a.75.75 0 01-.184.845l-.35.35a1.5 1.5 0 01-1.53.375c-1.628-.54-3.257-1.87-4.48-3.094-1.224-1.223-2.555-2.852-3.094-4.48a1.5 1.5 0 01.375-1.53l.35-.35Z" clip-rule="evenodd"/>
          </svg>
        </span>
        <div>
          <div class="text-xs font-semibold uppercase tracking-wide">WhatsApp</div>
          <div class="text-[11px] text-white/80">Chat with our team</div>
        </div>
      </div>
      <button
        type="button"
        class="p-1 text-white/80 hover:text-white"
        @click="open = false"
        aria-label="Close WhatsApp chat"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
          <path fill-rule="evenodd" d="M6.22 5.97a.75.75 0 011.06 0L12 10.69l4.72-4.72a.75.75 0 111.06 1.06L13.06 11.75l4.72 4.72a.75.75 0 11-1.06 1.06L12 12.81l-4.72 4.72a.75.75 0 11-1.06-1.06l4.72-4.72-4.72-4.72a.75.75 0 010-1.06Z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
    <div class="p-3 space-y-3">
      <p class="text-gray-700 text-sm">
        Hi there 👋<br>
        Have a question or need help with an order? Chat with us on WhatsApp.
      </p>
      <a
        href="{{ $whatsAppUrl }}"
        target="_blank"
        rel="noopener"
        class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-green-600 text-white text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
          <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75 0 1.74.458 3.37 1.26 4.78L2.25 21.75l5.07-1.23A9.708 9.708 0 0012 22.5c5.385 0 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-4.06 6.56a.75.75 0 01.845-.184l1.864.776a.75.75 0 01.41.995l-.38.952a.75.75 0 00.108.73c.45.62 1.057 1.226 1.676 1.676a.75.75 0 00.73.108l.952-.38a.75.75 0 01.995.41l.776 1.864a.75.75 0 01-.184.845l-.35.35a1.5 1.5 0 01-1.53.375c-1.628-.54-3.257-1.87-4.48-3.094-1.224-1.223-2.555-2.852-3.094-4.48a1.5 1.5 0 01.375-1.53l.35-.35Z" clip-rule="evenodd"/>
        </svg>
        <span>Start WhatsApp chat</span>
      </a>
      <p class="text-[11px] text-gray-400">
        WhatsApp will open in a new window.
      </p>
    </div>
  </div>

  {{-- Floating button --}}
  <button
    type="button"
    class="w-12 h-12 rounded-full shadow-lg bg-green-600 text-white flex items-center justify-center hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
    @click="open = !open"
    aria-label="Chat on WhatsApp"
  >
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
      <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75 0 1.74.458 3.37 1.26 4.78L2.25 21.75l5.07-1.23A9.708 9.708 0 0012 22.5c5.385 0 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-4.06 6.56a.75.75 0 01.845-.184l1.864.776a.75.75 0 01.41.995l-.38.952a.75.75 0 00.108.73c.45.62 1.057 1.226 1.676 1.676a.75.75 0 00.73.108l.952-.38a.75.75 0 01.995.41l.776 1.864a.75.75 0 01-.184.845l-.35.35a1.5 1.5 0 01-1.53.375c-1.628-.54-3.257-1.87-4.48-3.094-1.224-1.223-2.555-2.852-3.094-4.48a1.5 1.5 0 01.375-1.53l.35-.35Z" clip-rule="evenodd"/>
    </svg>
  </button>
</div>

