@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-semibold mb-4">My Profile</h1>

@if(session('status') === 'profile-updated')
  <x-alert type="success">Profile updated successfully.</x-alert>
@endif
@if(session('status') === 'password-updated')
  <x-alert type="success">Password updated successfully.</x-alert>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <div class="bg-white border rounded p-4">
    <h2 class="text-lg font-semibold mb-3">Profile Information</h2>
    <form method="post" action="{{ route('profile.update') }}" class="space-y-3">
      @csrf
      @method('patch')

      <div>
        <label class="block text-sm">Name</label>
        <input class="w-full border rounded px-3 py-2" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" required>
        @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="block text-sm">Email</label>
        <input class="w-full border rounded px-3 py-2" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required>
        @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="block text-sm">Estate / Building / Street</label>
        <input class="w-full border rounded px-3 py-2" name="address" type="text" value="{{ old('address', auth()->user()->address) }}">
        @error('address')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
          <label class="block text-sm">Town / Area</label>
          <input class="w-full border rounded px-3 py-2" name="city" type="text" value="{{ old('city', auth()->user()->city) }}">
          @error('city')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="block text-sm">County</label>
          @php($selectedCounty = old('state', auth()->user()->state))
          @if(($counties ?? collect())->isNotEmpty())
            <select class="w-full border rounded px-3 py-2" name="state">
              <option value="">Select county</option>
              @foreach($counties as $countyName)
                <option value="{{ $countyName }}" @selected($selectedCounty === $countyName)>{{ $countyName }}</option>
              @endforeach
            </select>
          @else
            <input class="w-full border rounded px-3 py-2" name="state" type="text" value="{{ $selectedCounty }}">
          @endif
          @error('state')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="block text-sm">Postal Code / P.O. Box</label>
          <input class="w-full border rounded px-3 py-2" name="postal_code" type="text" value="{{ old('postal_code', auth()->user()->postal_code) }}">
          @error('postal_code')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
      </div>

      <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
    </form>
  </div>

  <div class="bg-white border rounded p-4">
    <h2 class="text-lg font-semibold mb-3">Update Password</h2>
    <form method="post" action="{{ route('password.update') }}" class="space-y-3">
      @csrf
      @method('put')

      <div>
        <label class="block text-sm">Current Password</label>
        <input class="w-full border rounded px-3 py-2" name="current_password" type="password" autocomplete="current-password" required>
        @if($errors->updatePassword?->first('current_password'))
          <div class="text-red-600 text-sm">{{ $errors->updatePassword->first('current_password') }}</div>
        @endif
      </div>
      <div>
        <label class="block text-sm">New Password</label>
        <input class="w-full border rounded px-3 py-2" name="password" type="password" autocomplete="new-password" required>
        @if($errors->updatePassword?->first('password'))
          <div class="text-red-600 text-sm">{{ $errors->updatePassword->first('password') }}</div>
        @endif
      </div>
      <div>
        <label class="block text-sm">Confirm Password</label>
        <input class="w-full border rounded px-3 py-2" name="password_confirmation" type="password" autocomplete="new-password" required>
      </div>

      <button class="px-4 py-2 bg-blue-600 text-white rounded">Update Password</button>
    </form>
  </div>
</div>
@endsection
