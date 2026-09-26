@php($editing = isset($user))

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div>
    <label class="block text-sm font-medium">Name</label>
    <input name="name" value="{{ old('name', $editing ? $user->name : '') }}" class="w-full border rounded px-3 py-2" required>
    @error('name')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="block text-sm font-medium">Email</label>
    <input type="email" name="email" value="{{ old('email', $editing ? $user->email : '') }}" class="w-full border rounded px-3 py-2" required>
    @error('email')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="block text-sm font-medium">Role</label>
    <select name="user_type" class="w-full border rounded px-3 py-2" required>
      <option value="admin" @selected(old('user_type', $editing ? $user->user_type : 'buyer') === 'admin')>Admin</option>
      <option value="staff" @selected(old('user_type', $editing ? $user->user_type : 'buyer') === 'staff')>Staff</option>
      <option value="buyer" @selected(old('user_type', $editing ? $user->user_type : 'buyer') === 'buyer')>Buyer</option>
    </select>
    @error('user_type')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="block text-sm font-medium">Password {{ $editing ? '(leave blank to keep current)' : '' }}</label>
    <input type="password" name="password" class="w-full border rounded px-3 py-2" @if(!$editing) required @endif>
    @error('password')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="block text-sm font-medium">Confirm Password</label>
    <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" @if(!$editing) required @endif>
  </div>
  <div>
    <label class="block text-sm font-medium">Estate / Building / Street (optional)</label>
    <input name="address" value="{{ old('address', $editing ? $user->address : '') }}" class="w-full border rounded px-3 py-2">
    @error('address')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="block text-sm font-medium">Town / Area (optional)</label>
    <input name="city" value="{{ old('city', $editing ? $user->city : '') }}" class="w-full border rounded px-3 py-2">
    @error('city')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="block text-sm font-medium">County (optional)</label>
    @php($selectedCounty = old('state', $editing ? $user->state : ''))
    @if(($counties ?? collect())->isNotEmpty())
      <select name="state" class="w-full border rounded px-3 py-2">
        <option value="">Select county</option>
        @foreach($counties as $countyName)
          <option value="{{ $countyName }}" @selected($selectedCounty === $countyName)>{{ $countyName }}</option>
        @endforeach
      </select>
    @else
      <input name="state" value="{{ $selectedCounty }}" class="w-full border rounded px-3 py-2">
    @endif
    @error('state')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="block text-sm font-medium">Postal Code / P.O. Box (optional)</label>
    <input name="postal_code" value="{{ old('postal_code', $editing ? $user->postal_code : '') }}" class="w-full border rounded px-3 py-2">
    @error('postal_code')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
  </div>
</div>
