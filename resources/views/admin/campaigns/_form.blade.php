@php
  $selectedProducts = collect(old('bundle_products', $selectedProducts ?? []))->values();
  $rowCount = max($selectedProducts->count() + 4, 8);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div class="md:col-span-2">
    <label class="block text-sm">Title</label>
    <input type="text" name="title" value="{{ old('title', $campaign->title) }}" class="w-full border rounded px-3 py-2" required>
    @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm">Slug</label>
    <input type="text" name="slug" value="{{ old('slug', $campaign->slug) }}" class="w-full border rounded px-3 py-2" placeholder="hp-laptop-week">
    @error('slug')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm">Hero Badge</label>
    <input type="text" name="hero_badge" value="{{ old('hero_badge', $campaign->hero_badge) }}" class="w-full border rounded px-3 py-2" placeholder="Limited campaign">
    @error('hero_badge')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div class="md:col-span-2">
    <label class="block text-sm">Summary</label>
    <textarea name="summary" rows="3" class="w-full border rounded px-3 py-2" placeholder="A short hero summary for the landing page.">{{ old('summary', $campaign->summary) }}</textarea>
    @error('summary')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div class="md:col-span-2">
    <label class="block text-sm">Campaign Content</label>
    <textarea name="content" rows="8" class="w-full border rounded px-3 py-2" placeholder="Long-form copy, offer details, FAQs, and campaign messaging.">{{ old('content', $campaign->content) }}</textarea>
    @error('content')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm">Featured Image</label>
    <input type="file" name="featured_image" accept="image/*" class="w-full border rounded px-3 py-2">
    @error('featured_image')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    @if(!empty($campaign->featured_image))
      <img src="{{ asset('storage/'.$campaign->featured_image) }}" alt="Campaign image" class="mt-2 h-20 rounded border object-cover">
      <label class="mt-2 inline-flex items-center gap-2 text-xs text-gray-600">
        <input type="checkbox" name="remove_featured_image" value="1">
        <span>Remove current image</span>
      </label>
    @endif
  </div>

  <div>
    <label class="block text-sm">Promotion Code</label>
    <select name="promotion_id" class="w-full border rounded px-3 py-2">
      <option value="">None</option>
      @foreach($promotions as $promotion)
        <option value="{{ $promotion->id }}" @selected((string) old('promotion_id', $campaign->promotion_id) === (string) $promotion->id)>
          {{ $promotion->code }} {{ $promotion->active ? '' : '(inactive)' }}
        </option>
      @endforeach
    </select>
    @error('promotion_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm">CTA Label</label>
    <input type="text" name="cta_label" value="{{ old('cta_label', $campaign->cta_label) }}" class="w-full border rounded px-3 py-2" placeholder="Order on WhatsApp">
    @error('cta_label')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm">CTA URL</label>
    <input type="text" name="cta_url" value="{{ old('cta_url', $campaign->cta_url) }}" class="w-full border rounded px-3 py-2" placeholder="https://wa.me/... or /products">
    @error('cta_url')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div class="md:col-span-2">
    <label class="block text-sm">WhatsApp Message for Bundle CTA</label>
    <textarea name="whatsapp_message" rows="3" class="w-full border rounded px-3 py-2" placeholder="Hello, I would like the HP Laptop Week bundle.">{{ old('whatsapp_message', $campaign->whatsapp_message) }}</textarea>
    @error('whatsapp_message')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm">Meta Title</label>
    <input type="text" name="meta_title" value="{{ old('meta_title', $campaign->meta_title) }}" class="w-full border rounded px-3 py-2" placeholder="HP Laptop Week in Kenya | Nimtech">
    @error('meta_title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm">Meta Description</label>
    <input type="text" name="meta_description" value="{{ old('meta_description', $campaign->meta_description) }}" class="w-full border rounded px-3 py-2" placeholder="Launch-page description for ads and SEO.">
    @error('meta_description')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="inline-flex items-center gap-2 text-sm">
      <input type="checkbox" name="compare_enabled" value="1" @checked(old('compare_enabled', $campaign->compare_enabled ?? true))>
      <span>Show compare table</span>
    </label>
  </div>

  <div>
    <label class="inline-flex items-center gap-2 text-sm">
      <input type="checkbox" name="published" value="1" @checked(old('published', $campaign->published ?? true))>
      <span>Published</span>
    </label>
  </div>

  <div class="md:col-span-2 border-t pt-4 mt-2">
    <h2 class="text-lg font-semibold mb-2">Bundle Products</h2>
    <p class="text-xs text-gray-500 mb-3">Choose the products to feature in this campaign. These same items power the bundle add-to-cart action.</p>

    <div class="space-y-3">
      @for($i = 0; $i < $rowCount; $i++)
        @php($row = (array) ($selectedProducts[$i] ?? []))
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 border rounded p-3">
          <div class="md:col-span-8">
            <label class="block text-xs text-gray-600">Product</label>
            <select name="bundle_products[{{ $i }}][product_id]" class="w-full border rounded px-3 py-2">
              <option value="">Select product</option>
              @foreach($products as $product)
                <option value="{{ $product->id }}" @selected((string) old("bundle_products.$i.product_id", $row['product_id'] ?? '') === (string) $product->id)>
                  {{ $product->name }}@if($product->category) - {{ $product->category->name }}@endif
                </option>
              @endforeach
            </select>
            @error("bundle_products.$i.product_id")<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
          </div>
          <div class="md:col-span-2">
            <label class="block text-xs text-gray-600">Qty</label>
            <input type="number" min="1" max="99" name="bundle_products[{{ $i }}][quantity]" value="{{ old("bundle_products.$i.quantity", $row['quantity'] ?? 1) }}" class="w-full border rounded px-3 py-2">
            @error("bundle_products.$i.quantity")<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
          </div>
          <div class="md:col-span-2">
            <label class="block text-xs text-gray-600">Sort</label>
            <input type="number" min="0" max="9999" name="bundle_products[{{ $i }}][sort_order]" value="{{ old("bundle_products.$i.sort_order", $row['sort_order'] ?? $i) }}" class="w-full border rounded px-3 py-2">
            @error("bundle_products.$i.sort_order")<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
          </div>
        </div>
      @endfor
    </div>
  </div>
</div>
