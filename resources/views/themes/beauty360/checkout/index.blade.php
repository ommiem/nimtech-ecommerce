@extends('theme::layouts.app')

@section('content')
<x-breadcrumbs :items="[
  ['label' => 'Home', 'url' => route('products.index')],
  ['label' => 'Checkout']
]" />
<h1 class="text-2xl font-semibold">Checkout</h1>
<p class="text-sm text-gray-600 mb-6">Fast, secure checkout. Pay on Pickup by default.</p>

@guest
<div class="mb-6 rounded-xl border bg-white p-4 shadow-sm">
  <details class="group">
    <summary class="flex items-center justify-between cursor-pointer list-none">
      <div>
        <h2 class="text-lg font-semibold">Already have an account?</h2>
        <p class="text-sm text-gray-600">Log in to use saved details and track orders.</p>
      </div>
      <div class="flex items-center gap-2 text-sm text-blue-600">
        <span class="group-open:hidden">Log in</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-4 h-4 transition-transform group-open:rotate-180"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
      </div>
    </summary>
    <div class="mt-4 border-t pt-4">
      <form method="POST" action="{{ route('login') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @csrf
        <input type="hidden" name="redirect" value="checkout">
        <div>
          <label class="block text-sm font-medium">Email</label>
          <input class="w-full border rounded-lg px-3 py-2" type="email" name="email" autocomplete="username">
        </div>
        <div>
          <label class="block text-sm font-medium">Password</label>
          <input class="w-full border rounded-lg px-3 py-2" type="password" name="password" autocomplete="current-password">
        </div>
        <div class="sm:col-span-2">
          <button class="px-4 py-2 bg-gray-800 text-white rounded-lg">Login and Continue</button>
          <a class="ml-2 text-sm hover:underline" href="{{ route('password.request') }}">Forgot password?</a>
        </div>
      </form>
    </div>
  </details>
  <p class="mt-3 text-sm text-gray-600">No account? Continue below as a guest. You can create one after checkout.</p>
</div>
@endguest

@php
  $methods = $availablePaymentMethods ?? ['mpesa' => true, 'cod' => true];
  $mpesaEnabled = !empty($methods['mpesa']);
  $codEnabled = !empty($methods['cod']);
  $defaultPayment = $codEnabled ? 'cod' : ($mpesaEnabled ? 'mpesa' : 'cod');
  $countyOptions = collect($counties ?? [])->filter()->values();
  if ($countyOptions->isEmpty()) {
      $countyOptions = collect([
          'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo-Marakwet', 'Embu', 'Garissa', 'Homa Bay', 'Isiolo',
          'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga', 'Kisii', 'Kisumu', 'Kitui', 'Kwale',
          'Laikipia', 'Lamu', 'Machakos', 'Makueni', 'Mandera', 'Marsabit', 'Meru', 'Migori', 'Mombasa',
          "Murang'a", 'Nairobi', 'Nakuru', 'Nandi', 'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu', 'Siaya',
          'Taita-Taveta', 'Tana River', 'Tharaka-Nithi', 'Trans Nzoia', 'Turkana', 'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot',
      ]);
  }
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
  <form action="{{ route('checkout.process') }}" method="POST" class="lg:col-span-2 space-y-6" x-data="{
      billingSame: true,
      profile: @js(['full_name'=>auth()->user()->name ?? '', 'email'=>auth()->user()->email ?? '', 'phone'=>auth()->user()->phone ?? '', 'address'=>auth()->user()->address ?? '', 'city'=>auth()->user()->city ?? '', 'state'=>auth()->user()->state ?? '', 'postal_code'=>auth()->user()->postal_code ?? '']),
      addresses: @js(($addresses ?? collect())->values()),
      useProfile(){ if(!this.profile) return; $refs.full_name.value=this.profile.full_name||''; $refs.email.value=this.profile.email||''; $refs.phone.value=this.profile.phone||''; $refs.address.value=this.profile.address||''; $refs.city.value=this.profile.city||''; $refs.state.value=this.profile.state||''; $refs.postal_code.value=this.profile.postal_code||''; },
      useAddress(id){ const a = this.addresses.find(x=>x.id===Number(id)); if(!a) return; $refs.full_name.value=a.full_name||''; $refs.email.value=a.email||''; $refs.phone.value=a.phone||''; $refs.address.value=a.address||''; $refs.city.value=a.city||''; $refs.state.value=a.state||''; $refs.postal_code.value=a.postal_code||''; }
    }">
    @csrf
    <input type="hidden" name="payment_method" value="{{ $defaultPayment }}">

    <div class="rounded-xl border bg-white p-4 shadow-sm">
      <div class="flex items-start justify-between gap-3">
        <div>
          <h2 class="text-lg font-semibold">Contact details</h2>
          <p class="text-sm text-gray-600">We will use this to confirm and coordinate delivery.</p>
        </div>
        <span class="text-xs px-2 py-1 rounded-full bg-green-50 text-green-700 border border-green-200">Pay on Pickup</span>
      </div>
      <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium">Full Name</label>
          <input class="w-full border rounded-lg px-3 py-2" name="full_name" x-ref="full_name" value="{{ old('full_name', auth()->user()->name ?? '') }}" required>
          @error('full_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium">Phone Number (Kenya)</label>
          <input class="w-full border rounded-lg px-3 py-2" type="tel" name="phone" x-ref="phone" value="{{ old('phone') }}" placeholder="e.g. 0712345678 or +254712345678" required>
          <p class="mt-1 text-xs text-gray-500">Used for delivery updates and order confirmation.</p>
          @error('phone')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium">Email</label>
          <input class="w-full border rounded-lg px-3 py-2" type="email" name="email" x-ref="email" value="{{ old('email', auth()->user()->email ?? '') }}" required>
          @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>

    <div class="rounded-xl border bg-white p-4 shadow-sm">
      <div class="flex items-start justify-between gap-3">
        <div>
          <h2 class="text-lg font-semibold">Delivery address</h2>
          <p class="text-sm text-gray-600">Where should we deliver your order?</p>
        </div>
      </div>
      @auth
        @if(($addresses ?? collect())->count())
          <div class="mt-3 flex flex-col sm:flex-row gap-2 sm:items-end">
            <div class="flex-1">
              <label class="block text-sm font-medium">Saved Addresses</label>
              <select x-ref="address_select" class="w-full border rounded-lg px-3 py-2">
                @foreach(($addresses ?? collect()) as $a)
                  <option value="{{ $a->id }}">{{ $a->label ? $a->label.' - ' : '' }}{{ $a->address }}, {{ $a->city }}</option>
                @endforeach
              </select>
            </div>
            <div class="flex gap-2">
              <button type="button" class="px-3 py-2 text-sm border rounded-lg hover:bg-gray-50" @click="useAddress($refs.address_select.value)">Use</button>
              <button type="button" class="px-3 py-2 text-sm border rounded-lg hover:bg-gray-50" @click="useProfile()">Use profile</button>
            </div>
          </div>
        @endif
      @endauth
      <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium">Estate / Building / Street</label>
          <input class="w-full border rounded-lg px-3 py-2" name="address" x-ref="address" value="{{ old('address', auth()->user()->address ?? '') }}" placeholder="e.g. Kariobangi South, House B12, Mumias Road" required>
          <p class="mt-1 text-xs text-gray-500">Add an easy landmark if possible (near stage, school, petrol station).</p>
          @error('address')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium">Town / Area</label>
          <input class="w-full border rounded-lg px-3 py-2" name="city" x-ref="city" value="{{ old('city', auth()->user()->city ?? '') }}" placeholder="e.g. Nairobi CBD, Rongai, Kitengela">
        </div>
        <div>
          <label class="block text-sm font-medium">County</label>
          @php($selectedCounty = old('state', auth()->user()->state ?? ''))
          @if($countyOptions->isNotEmpty())
            <select class="w-full border rounded-lg px-3 py-2" name="state" x-ref="state">
              <option value="">Select county</option>
              @foreach($countyOptions as $countyName)
                <option value="{{ $countyName }}" @selected($selectedCounty === $countyName)>{{ $countyName }}</option>
              @endforeach
            </select>
          @else
            <input class="w-full border rounded-lg px-3 py-2" name="state" x-ref="state" value="{{ $selectedCounty }}" placeholder="e.g. Nairobi">
          @endif
        </div>
        <div>
          <label class="block text-sm font-medium">Postal Code / P.O. Box (optional)</label>
          <input class="w-full border rounded-lg px-3 py-2" name="postal_code" x-ref="postal_code" value="{{ old('postal_code', auth()->user()->postal_code ?? '') }}" placeholder="e.g. 00100">
        </div>
      </div>
    </div>

    @guest
    <div class="rounded-xl border bg-white p-4 shadow-sm">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h2 class="text-lg font-semibold">Create an account (optional)</h2>
          <p class="text-sm text-gray-600">Save your details for faster checkout next time.</p>
        </div>
        <label class="inline-flex items-center gap-2 text-sm">
          <input class="h-4 w-4 accent-blue-600" type="checkbox" name="create_account" value="1" x-data x-on:change="$dispatch('toggle-create-account', {checked: $event.target.checked})">
          <span class="font-medium">Create account</span>
        </label>
      </div>
      <div class="mt-4" x-data="{show:false}" x-on:toggle-create-account.window="show=$event.detail.checked" x-show="show">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">Name</label>
            <input class="w-full border rounded-lg px-3 py-2" name="name" value="{{ old('name') }}">
            @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="block text-sm font-medium">Password</label>
            <input class="w-full border rounded-lg px-3 py-2" type="password" name="password">
            @error('password')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="block text-sm font-medium">Confirm Password</label>
            <input class="w-full border rounded-lg px-3 py-2" type="password" name="password_confirmation">
          </div>
        </div>
      </div>
    </div>
    @endguest

    <div class="rounded-xl border bg-white p-4 shadow-sm">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h2 class="text-lg font-semibold">Billing</h2>
          <p class="text-sm text-gray-600">Use a different billing address if needed.</p>
        </div>
        <label class="inline-flex items-center gap-2 text-sm">
          <input type="checkbox" name="billing_same" value="1" x-model="billingSame">
          <span>Same as delivery</span>
        </label>
      </div>
      <template x-if="!billingSame">
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 border-t pt-4">
          <div class="sm:col-span-2 font-semibold text-sm">Billing Address</div>
          <div>
            <label class="block text-sm font-medium">Full Name</label>
            <input class="w-full border rounded-lg px-3 py-2" name="billing_full_name" value="{{ old('billing_full_name') }}">
          </div>
          <div>
            <label class="block text-sm font-medium">Email</label>
            <input class="w-full border rounded-lg px-3 py-2" type="email" name="billing_email" value="{{ old('billing_email') }}">
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium">Estate / Building / Street</label>
            <input class="w-full border rounded-lg px-3 py-2" name="billing_address" value="{{ old('billing_address') }}" placeholder="e.g. Mvita, Tom Mboya Avenue, Shop 4">
          </div>
          <div>
            <label class="block text-sm font-medium">Town / Area</label>
            <input class="w-full border rounded-lg px-3 py-2" name="billing_city" value="{{ old('billing_city') }}" placeholder="e.g. Mombasa CBD">
          </div>
          <div>
            <label class="block text-sm font-medium">County</label>
            @php($selectedBillingCounty = old('billing_state'))
            @if($countyOptions->isNotEmpty())
              <select class="w-full border rounded-lg px-3 py-2" name="billing_state">
                <option value="">Select county</option>
                @foreach($countyOptions as $countyName)
                  <option value="{{ $countyName }}" @selected($selectedBillingCounty === $countyName)>{{ $countyName }}</option>
                @endforeach
              </select>
            @else
              <input class="w-full border rounded-lg px-3 py-2" name="billing_state" value="{{ $selectedBillingCounty }}" placeholder="e.g. Nairobi">
            @endif
          </div>
          <div>
            <label class="block text-sm font-medium">Postal Code / P.O. Box (optional)</label>
            <input class="w-full border rounded-lg px-3 py-2" name="billing_postal_code" value="{{ old('billing_postal_code') }}" placeholder="e.g. 00100">
          </div>
        </div>
      </template>
    </div>

    @auth
    <div class="rounded-xl border bg-white p-4 shadow-sm">
      <label class="inline-flex items-center gap-2 text-sm">
        <input type="checkbox" name="save_address" value="1">
        <span>Save this address to my profile</span>
      </label>
    </div>
    @endauth

    <div class="rounded-xl border bg-white p-4 shadow-sm">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-xs text-gray-500">By placing your order you agree to our Terms and Privacy Policy.</p>
        <button class="w-full sm:w-auto px-5 py-3 bg-blue-600 text-white rounded-lg font-medium">Place Order</button>
      </div>
    </div>
  </form>

  <aside class="bg-white border rounded-xl p-4 shadow-sm lg:sticky lg:top-24 h-fit">
    <h2 class="text-lg font-semibold mb-3">Order Summary</h2>
    <div class="space-y-2 text-sm">
      @foreach(($cart['lines'] ?? []) as $line)
        <div class="flex justify-between">
          <span>{{ $line['product']->name }} x {{ $line['quantity'] }}</span>
          <span>{{ currency_format($line['line_total']) }}</span>
        </div>
      @endforeach
      <div class="border-t mt-2 pt-2 flex justify-between font-semibold">
        <span>Total</span>
        <span>{{ currency_format($cart['total'] ?? 0) }}</span>
      </div>
      @if(!empty($cart['discount']))
      <div class="flex justify-between text-green-600">
        <span>Discount</span>
        <span>-{{ currency_format($cart['discount']) }}</span>
      </div>
      @endif
    </div>
    <div class="mt-3 text-xs text-gray-500">Payment method: Pay on Pickup.</div>
  </aside>
</div>
@endsection
