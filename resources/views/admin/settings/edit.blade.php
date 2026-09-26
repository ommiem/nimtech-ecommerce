@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Settings</h1>

@php
  $rawSpecialTiles = $setting->header_special_tiles ?? null;
  $specialTiles = collect(json_decode((string) ($rawSpecialTiles ?? '[]'), true));
  if (($rawSpecialTiles === null || trim((string) $rawSpecialTiles) === '') && $specialTiles->isEmpty()) {
      $specialTiles = collect(config('header.special_tiles', []));
  }
  $specialTiles = $specialTiles->values();
@endphp

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="bg-white border rounded p-4 max-w-3xl">
  @csrf
  @method('PUT')
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
      <label class="block text-sm">Site Name</label>
      <input type="text" name="site_name" value="{{ old('site_name', $setting->site_name ?? 'Shoply') }}" class="w-full border rounded px-3 py-2" required>
      @error('site_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Contact Email</label>
      <input type="email" name="contact_email" value="{{ old('contact_email', $setting->contact_email) }}" class="w-full border rounded px-3 py-2">
      @error('contact_email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Contact Phone</label>
      <input type="text" name="contact_phone" value="{{ old('contact_phone', $setting->contact_phone) }}" class="w-full border rounded px-3 py-2">
      @error('contact_phone')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm">Contact Address</label>
      <input type="text" name="contact_address" value="{{ old('contact_address', $setting->contact_address) }}" class="w-full border rounded px-3 py-2">
      @error('contact_address')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Currency Code</label>
      <input type="text" name="currency_code" value="{{ old('currency_code', $setting->currency_code ?? 'USD') }}" class="w-full border rounded px-3 py-2" required>
      @error('currency_code')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Currency Symbol</label>
      <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $setting->currency_symbol ?? '$') }}" class="w-full border rounded px-3 py-2" required>
      @error('currency_symbol')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Currency Position</label>
      <select name="currency_position" class="w-full border rounded px-3 py-2" required>
        <option value="left" @selected(old('currency_position', $setting->currency_position ?? 'left')==='left')>Left (e.g. $99.00)</option>
        <option value="right" @selected(old('currency_position', $setting->currency_position ?? 'left')==='right')>Right (e.g. 99.00 $)</option>
      </select>
      @error('currency_position')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm">Logo</label>
      <input type="file" name="logo" accept="image/*" class="w-full border rounded px-3 py-2">
      @error('logo')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      @if(!empty($setting->logo_path))
        <div class="mt-2">
          <img src="{{ asset('storage/'.$setting->logo_path) }}" alt="Current logo" class="h-10 object-contain border rounded bg-white p-1">
        </div>
      @endif
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm">Favicon (PNG or ICO, ideally 32x32)</label>
      <input type="file" name="favicon" accept="image/png,image/x-icon" class="w-full border rounded px-3 py-2">
      @error('favicon')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      @if(!empty($setting->favicon_path))
        <div class="mt-2 flex items-center gap-2">
          <img src="{{ asset('storage/'.$setting->favicon_path) }}" alt="Current favicon" class="h-8 w-8 object-contain border rounded bg-white p-1">
          <span class="text-xs text-gray-600">{{ basename($setting->favicon_path) }}</span>
        </div>
      @endif
    </div>
    <div>
      <label class="block text-sm">Theme Color</label>
      <input type="color" name="theme_color" value="{{ old('theme_color', $setting->theme_color ?? '#2563eb') }}" class="h-10 w-16 border rounded p-1">
      <p class="text-xs text-gray-500 mt-1">Pick a primary brand color (hex, e.g. #2563eb).</p>
      @error('theme_color')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Active Theme</label>
      <select name="active_theme" class="w-full border rounded px-3 py-2">
        @foreach(($themes ?? ['nimtech','aspirenoted']) as $t)
          <option value="{{ $t }}" @selected(old('active_theme', $setting->active_theme ?? 'nimtech')===$t)>{{ ucfirst($t) }}</option>
        @endforeach
      </select>
      <p class="text-xs text-gray-500 mt-1">Controls which set of views under resources/views/themes/ is used.</p>
      @error('active_theme')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Deals Under (KES)</label>
      <input type="number" step="0.01" min="0" name="deals_under_threshold" value="{{ old('deals_under_threshold', $setting->deals_under_threshold) }}" class="w-full border rounded px-3 py-2">
      <p class="text-xs text-gray-500 mt-1">Used on the home page "Top deals under" shelf.</p>
      @error('deals_under_threshold')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm">Homepage CTA Heading</label>
      <input type="text" name="homepage_cta_heading" value="{{ old('homepage_cta_heading', $setting->homepage_cta_heading) }}" class="w-full border rounded px-3 py-2">
      @error('homepage_cta_heading')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm">Homepage CTA Subtext</label>
      <input type="text" name="homepage_cta_subtext" value="{{ old('homepage_cta_subtext', $setting->homepage_cta_subtext) }}" class="w-full border rounded px-3 py-2">
      @error('homepage_cta_subtext')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2 border-t pt-4 mt-4">
      <h2 class="text-lg font-semibold mb-2">Header Promotions</h2>
      <div>
        <label class="block text-sm">Top Announcement Text</label>
        <input type="text" name="header_notice_text" value="{{ old('header_notice_text', $setting->header_notice_text) }}" class="w-full border rounded px-3 py-2" placeholder="Free delivery for orders over KES5,000.00 - New arrivals every week!">
        <p class="text-xs text-gray-500 mt-1">Shown in the top colored strip above the header.</p>
        @error('header_notice_text')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>
      <div class="mt-4">
        <div class="text-sm font-medium">Special Tiles (before categories)</div>
        <p class="text-xs text-gray-500 mb-3">Configure up to 4 tiles like "Valentine Sale". Leave label, URL, and image empty to disable a slot.</p>
        <div class="space-y-4">
          @for($i = 0; $i < 4; $i++)
            @php
              $tile = (array) ($specialTiles[$i] ?? []);
              $tileImage = (string) ($tile['image'] ?? '');
              $tileImageSrc = null;
              if ($tileImage !== '') {
                  if (\Illuminate\Support\Str::startsWith($tileImage, ['http://', 'https://'])) {
                      $tileImageSrc = $tileImage;
                  } elseif (\Illuminate\Support\Str::startsWith($tileImage, ['images/', 'storage/'])) {
                      $tileImageSrc = asset($tileImage);
                  } else {
                      $tileImageSrc = asset('storage/'.$tileImage);
                  }
              }
            @endphp
            <div class="border rounded p-3">
              <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                <div class="text-sm font-medium">Tile {{ $i + 1 }}</div>
                <div class="flex items-center gap-2">
                  <button
                    type="submit"
                    formaction="{{ route('admin.settings.header-tiles.update', $i) }}"
                    class="px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded hover:bg-blue-700"
                  >
                    Save Tile
                  </button>
                  <button
                    type="submit"
                    form="delete-header-tile-{{ $i }}"
                    class="px-3 py-1.5 text-xs font-medium bg-red-600 text-white rounded hover:bg-red-700"
                    onclick="return confirm('Delete tile {{ $i + 1 }}?')"
                  >
                    Delete Tile
                  </button>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                  <label class="block text-xs text-gray-600">Label</label>
                  <input type="text" name="special_tiles[{{ $i }}][label]" value="{{ old('special_tiles.'.$i.'.label', $tile['label'] ?? '') }}" class="w-full border rounded px-3 py-2" placeholder="Valentine Sale">
                  @error('special_tiles.'.$i.'.label')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                  <label class="block text-xs text-gray-600">URL</label>
                  <input type="text" name="special_tiles[{{ $i }}][url]" value="{{ old('special_tiles.'.$i.'.url', $tile['url'] ?? '') }}" class="w-full border rounded px-3 py-2" placeholder="/deals">
                  @error('special_tiles.'.$i.'.url')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                  <label class="block text-xs text-gray-600">Tile CSS Classes</label>
                  <input type="text" name="special_tiles[{{ $i }}][bg]" value="{{ old('special_tiles.'.$i.'.bg', $tile['bg'] ?? 'bg-gray-900 text-white') }}" class="w-full border rounded px-3 py-2" placeholder="bg-rose-600 text-white">
                  @error('special_tiles.'.$i.'.bg')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
              </div>
              <div class="mt-3">
                <label class="block text-xs text-gray-600">Image (optional)</label>
                <input type="file" name="special_tile_images[{{ $i }}]" accept="image/*" class="w-full border rounded px-3 py-2">
                @error('special_tile_images.'.$i)<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                @if($tileImageSrc)
                  <div class="mt-2 flex items-end gap-3">
                    <img src="{{ $tileImageSrc }}" alt="Tile {{ $i + 1 }} image" class="h-16 w-16 rounded object-cover border">
                    <button
                      type="submit"
                      form="delete-header-tile-{{ $i }}"
                      class="px-3 py-1.5 text-xs font-medium bg-red-600 text-white rounded hover:bg-red-700"
                      onclick="return confirm('Delete tile {{ $i + 1 }}?')"
                    >
                      Delete
                    </button>
                  </div>
                @endif
              </div>
            </div>
          @endfor
        </div>
      </div>
    </div>
    <div class="md:col-span-2 border-t pt-4 mt-4">
      <h2 class="text-lg font-semibold mb-2">Payment Methods</h2>
      <p class="text-xs text-gray-500 mb-3">Enable or disable checkout payment options.</p>
      @php
        $enableMpesa = old('enable_mpesa', ($setting->enable_mpesa ?? true)) ? true : false;
        $enableCod = old('enable_cod', ($setting->enable_cod ?? true)) ? true : false;
      @endphp
      <div class="space-y-2">
        <label class="inline-flex items-center gap-2 text-sm">
          <input type="checkbox" name="enable_mpesa" value="1" @checked($enableMpesa)>
          <span>M-Pesa (STK Push)</span>
        </label>
        <label class="inline-flex items-center gap-2 text-sm">
          <input type="checkbox" name="enable_cod" value="1" @checked($enableCod)>
          <span>Cash on Delivery</span>
        </label>
      </div>
    </div>
    <div class="md:col-span-2 border-t pt-4 mt-4">
      <h2 class="text-lg font-semibold mb-2">Shipping & Tax</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm">Flat Shipping Rate</label>
          <input type="number" step="0.01" min="0" name="shipping_flat_rate" value="{{ old('shipping_flat_rate', $setting->shipping_flat_rate) }}" class="w-full border rounded px-3 py-2">
          <p class="text-xs text-gray-500 mt-1">Optional flat shipping charge added to every order.</p>
          @error('shipping_flat_rate')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="block text-sm">Tax Rate (%)</label>
          <input type="number" step="0.01" min="0" name="tax_rate" value="{{ old('tax_rate', $setting->tax_rate) }}" class="w-full border rounded px-3 py-2">
          <p class="text-xs text-gray-500 mt-1">Percentage tax applied to the cart subtotal after discounts.</p>
          @error('tax_rate')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
    <div class="md:col-span-2 border-t pt-4 mt-4">
      <h2 class="text-lg font-semibold mb-2">Marketing Tracking</h2>
      <p class="text-xs text-gray-500 mb-3">Add pixel IDs and scripts for retargeting, conversion tracking, and analytics.</p>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm">GA4 Measurement ID</label>
          <input type="text" name="ga4_measurement_id" value="{{ old('ga4_measurement_id', $setting->ga4_measurement_id) }}" class="w-full border rounded px-3 py-2" placeholder="G-XXXXXXXXXX">
          @error('ga4_measurement_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="block text-sm">Google Ads ID</label>
          <input type="text" name="google_ads_id" value="{{ old('google_ads_id', $setting->google_ads_id) }}" class="w-full border rounded px-3 py-2" placeholder="AW-123456789">
          @error('google_ads_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="block text-sm">Meta Pixel ID</label>
          <input type="text" name="meta_pixel_id" value="{{ old('meta_pixel_id', $setting->meta_pixel_id) }}" class="w-full border rounded px-3 py-2" placeholder="123456789012345">
          @error('meta_pixel_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="block text-sm">TikTok Pixel ID</label>
          <input type="text" name="tiktok_pixel_id" value="{{ old('tiktok_pixel_id', $setting->tiktok_pixel_id) }}" class="w-full border rounded px-3 py-2" placeholder="C123ABC456DEF">
          @error('tiktok_pixel_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm">Custom Head Scripts</label>
          <textarea name="custom_head_scripts" rows="6" class="w-full border rounded px-3 py-2" placeholder="<script>...</script>">{{ old('custom_head_scripts', $setting->custom_head_scripts) }}</textarea>
          <p class="text-xs text-gray-500 mt-1">Rendered inside the public site &lt;head&gt; after built-in pixels.</p>
          @error('custom_head_scripts')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm">Custom Body Scripts</label>
          <textarea name="custom_body_scripts" rows="6" class="w-full border rounded px-3 py-2" placeholder="<noscript>...</noscript>">{{ old('custom_body_scripts', $setting->custom_body_scripts) }}</textarea>
          <p class="text-xs text-gray-500 mt-1">Rendered just after the opening public site &lt;body&gt; tag.</p>
          @error('custom_body_scripts')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
  </div>
  <div class="mt-4 flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save Settings</button>
    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
  </div>
</form>
@for($i = 0; $i < 4; $i++)
  <form id="delete-header-tile-{{ $i }}" action="{{ route('admin.settings.header-tiles.destroy', $i) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
  </form>
@endfor
@endsection
