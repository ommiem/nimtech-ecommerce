@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-semibold">Edit Product</h1>
  <div class="flex items-center gap-2">
    <a
      href="{{ route('products.show', ['productSlug' => $product->canonical_slug]) }}"
      target="_blank"
      rel="noopener"
      class="px-3 py-2 border rounded bg-white text-sm hover:bg-gray-50"
    >View public page</a>
    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product? This cannot be undone.')">
      @csrf
      @method('DELETE')
      <button class="px-3 py-2 bg-red-600 text-white rounded">Delete</button>
    </form>
  </div>
</div>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white border rounded p-4 max-w-3xl">
  @csrf
  @method('PUT')
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm">Brand (optional)</label>
      <select name="brand_id" class="w-full border rounded px-3 py-2">
        <option value="">Select...</option>
        @foreach(\App\Models\Brand::orderBy('name')->get() as $b)
          <option value="{{ $b->id }}" @selected(old('brand_id', $product->brand_id)==$b->id)>{{ $b->name }}</option>
        @endforeach
      </select>
      @error('brand_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Name</label>
      <input name="name" value="{{ old('name', $product->name) }}" class="w-full border rounded px-3 py-2" required>
      @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Slug</label>
      <input name="slug" value="{{ old('slug', $product->slug) }}" class="w-full border rounded px-3 py-2">
      @error('slug')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Category</label>
      <select name="category_id" class="w-full border rounded px-3 py-2" required>
        @foreach($categories as $c)
          <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id)==$c->id)>{{ $c->name }}</option>
        @endforeach
      </select>
      @error('category_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Price</label>
      <input name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price) }}" class="w-full border rounded px-3 py-2" required>
      @error('price')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Stock</label>
      <input name="stock" type="number" min="0" value="{{ old('stock', $product->stock) }}" class="w-full border rounded px-3 py-2" required>
      @error('stock')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm">Description</label>
      <textarea name="description" rows="8" class="w-full border rounded px-3 py-2 tinymce-editor">{{ old('description', $product->description) }}</textarea>
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">SEO content description</label>
      <p class="mb-2 text-xs text-gray-500">Long-form buying guide content shown near the bottom of the product page.</p>
      <textarea name="seo_content" rows="10" class="w-full border rounded px-3 py-2 tinymce-editor">{{ old('seo_content', $product->seo_content) }}</textarea>
      @error('seo_content')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm">Image (optional)</label>
      <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
      @error('image')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      @if($product->image)
        <div class="mt-2">
          <img src="{{ asset('storage/'.$product->image) }}" alt="Current image" class="h-24 object-cover rounded border">
        </div>
      @endif
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm">Gallery Images (optional)</label>
      <input type="file" name="images[]" accept="image/*" multiple class="w-full border rounded px-3 py-2">
      @error('images.*')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      @if($product->images && $product->images->count())
        <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-2">
          @foreach($product->images as $img)
          <div class="relative">
            <img src="{{ asset('storage/'.$img->path) }}" class="h-24 w-full object-cover rounded border" loading="lazy">
            <button form="img-del-{{ $img->id }}" class="absolute top-1 right-1 px-1.5 py-0.5 text-xs bg-red-600 text-white rounded" onclick="return confirm('Delete this image?')">Delete</button>
          </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
  <div class="mt-4 flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
  </div>
</form>
@if($product->images && $product->images->count())
  @foreach($product->images as $img)
    <form id="img-del-{{ $img->id }}" action="{{ route('admin.products.images.destroy', [$product,$img]) }}" method="POST" class="hidden">
      @csrf
      @method('DELETE')
    </form>
  @endforeach
@endif
<script src="{{ asset('js/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script>
  if (window.tinymce) {
    tinymce.init({
      selector: 'textarea.tinymce-editor',
      height: 350,
      menubar: false,
      plugins: 'lists link table code',
      toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link table | removeformat | code',
      branding: false,
      convert_urls: false,
      content_css: false,
      setup: (ed) => { ed.on('change keyup', () => ed.save()); }
    });
  }
</script>
@endsection
