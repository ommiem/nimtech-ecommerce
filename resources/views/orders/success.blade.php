@extends('theme::layouts.app')

@section('content')
<div class="max-w-xl text-center mx-auto bg-white border rounded p-8">
  <div class="text-5xl mb-3">dYZ%</div>
  <h1 class="text-3xl font-semibold mb-4">Thank you!</h1>
  <p>Your order <strong>#{{ $order->id }}</strong> has been placed successfully.</p>
  <p class="mt-2">Status: <span id="order-status" class="font-semibold">{{ ucfirst($order->status) }}</span></p>
  <p class="mt-2">Total: <span class="font-semibold">{{ currency_format($order->total) }}</span></p>
  <div class="mt-6 space-x-2">
    <a href="{{ route('products.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Continue Shopping</a>
    <a href="{{ route('cart.index') }}" class="px-4 py-2 bg-gray-200 rounded">View Cart</a>
    @auth
      <a href="{{ route('account.orders.index') }}" class="px-4 py-2 bg-gray-200 rounded">My Orders</a>
      <a href="{{ route('account.orders.show', $order) }}" class="px-4 py-2 bg-gray-200 rounded">View Order</a>
      @php($settings = \App\Models\Setting::getCached())
      @if(($order->payment_method ?? 'mpesa') === 'cod' && $order->status !== 'paid' && ($settings?->enable_mpesa ?? true))
        <form action="{{ route('account.orders.paynow', $order) }}" method="POST" class="inline">
          @csrf
          <input type="hidden" name="phone" value="{{ $order->mpesa_phone ?? $order->phone }}">
          <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Pay Now with M-Pesa</button>
        </form>
      @endif
    @endauth
  </div>

  <!-- Toast -->
  <div id="toast" class="fixed top-4 left-1/2 -translate-x-1/2 px-4 py-2 rounded shadow text-white hidden"></div>

  <script>
    // Poll order status for up to 60s to reflect payment result
    (function(){
      // Only auto-poll for M-Pesa payments
      const isMpesa = "{{ ($order->payment_method ?? 'mpesa') === 'mpesa' ? '1' : '0' }}" === '1';
      if(!isMpesa) return;
      const statusEl = document.getElementById('order-status');
      if(!statusEl) return;
      const end = Date.now() + 60000; // 60s
      const url = "{{ route('orders.status', $order) }}";
      const redirectUrl = @auth "{{ route('account.orders.show', $order) }}" @else null @endauth;
      let currentStatus = (statusEl.textContent || '').trim().toLowerCase();

      function showToast(message, type){
        const el = document.getElementById('toast');
        if(!el) return;
        el.textContent = message;
        el.classList.remove('hidden','bg-green-600','bg-red-600');
        el.classList.add(type === 'error' ? 'bg-red-600' : 'bg-green-600');
        setTimeout(() => el.classList.add('hidden'), 4000);
      }

      const tick = () => {
        fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
          .then(r => r.json())
          .then(j => {
            if(j && j.status){
              const newStatus = (j.status || '').toLowerCase();
              statusEl.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
              if(newStatus !== currentStatus){
                currentStatus = newStatus;
                if(newStatus === 'paid'){
                  showToast('Payment confirmed. Thank you!', 'success');
                  if(redirectUrl){ setTimeout(() => { window.location.href = redirectUrl; }, 1500); }
                  return; // stop further polling
                }
                if(newStatus === 'failed'){
                  showToast('Payment failed. Please retry.', 'error');
                  return; // stop further polling
                }
              }
            }
            if(Date.now() < end) setTimeout(tick, 3000);
          })
          .catch(() => { if(Date.now() < end) setTimeout(tick, 5000); });
      };
      setTimeout(tick, 3000);
    })();
  </script>
</div>
@endsection

