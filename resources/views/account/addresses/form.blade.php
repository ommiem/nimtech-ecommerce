@csrf
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
  <div>
    <label class="block text-sm">Label</label>
    <input class="w-full border rounded px-3 py-2" name="label" value="{{ old('label', $address->label ?? '') }}">
  </div>
  <div>
    <label class="block text-sm">Full Name</label>
    <input class="w-full border rounded px-3 py-2" name="full_name" value="{{ old('full_name', $address->full_name ?? auth()->user()->name) }}" required>
  </div>
  <div>
    <label class="block text-sm">Email</label>
    <input class="w-full border rounded px-3 py-2" name="email" type="email" value="{{ old('email', $address->email ?? auth()->user()->email) }}">
  </div>
  <div>
    <label class="block text-sm">Phone</label>
    <input class="w-full border rounded px-3 py-2" name="phone" value="{{ old('phone', $address->phone ?? auth()->user()->phone ?? '') }}">
  </div>
  <div class="sm:col-span-2">
    <label class="block text-sm">Estate / Building / Street</label>
    <input class="w-full border rounded px-3 py-2" name="address" value="{{ old('address', $address->address ?? '') }}" required>
  </div>
  <div>
    <label class="block text-sm">Town / Area</label>
    <input class="w-full border rounded px-3 py-2" name="city" value="{{ old('city', $address->city ?? '') }}">
  </div>
  <div>
    <label class="block text-sm">County</label>
    @php($selectedCounty = old('state', isset($address) ? $address->state : ''))
    @if(($counties ?? collect())->isNotEmpty())
      <select class="w-full border rounded px-3 py-2" name="state">
        <option value="">Select county</option>
        @foreach($counties as $countyName)
          <option value="{{ $countyName }}" @selected($selectedCounty === $countyName)>{{ $countyName }}</option>
        @endforeach
      </select>
    @else
      <input class="w-full border rounded px-3 py-2" name="state" value="{{ $selectedCounty }}">
    @endif
  </div>
  <div>
    <label class="block text-sm">Postal Code / P.O. Box</label>
    <input class="w-full border rounded px-3 py-2" name="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}">
  </div>
  <div class="sm:col-span-2">
    <label class="inline-flex items-center gap-2 text-sm">
      <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $address->is_default ?? false))>
      <span>Set as default</span>
    </label>
  </div>
</div>
