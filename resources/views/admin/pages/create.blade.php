@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-semibold mb-4">New Page</h1>

<form action="{{ route('admin.pages.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border rounded p-4 max-w-3xl">
  @csrf
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm">Title</label>
      <input name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
      @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Slug (optional)</label>
      <input name="slug" value="{{ old('slug') }}" class="w-full border rounded px-3 py-2">
      @error('slug')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm">Featured Image</label>
      <input type="file" name="featured_image" accept="image/*" class="w-full border rounded px-3 py-2 bg-white">
      <div class="text-xs text-gray-500 mt-1">Recommended: JPG/PNG/WebP, up to 4MB.</div>
      @error('featured_image')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm">Category Products (optional)</label>
      <select name="category_id" class="w-full border rounded px-3 py-2 bg-white">
        <option value="">-- No category --</option>
        @foreach($categories as $category)
          <option value="{{ $category->id }}" @selected((string)old('category_id') === (string)$category->id)>{{ $category->name }}</option>
        @endforeach
      </select>
      <div class="text-xs text-gray-500 mt-1">Select a category to show its products on the public page.</div>
      @error('category_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2">
      <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="published" value="1" {{ old('published', 1) ? 'checked' : '' }}> <span>Published</span></label>
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm">Content</label>
      <textarea name="content" rows="12" class="w-full border rounded px-3 py-2 tinymce-editor">{{ old('content') }}</textarea>
      @error('content')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="mt-4 flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
    <a href="{{ route('admin.pages.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
  </div>
</form>

<script src="{{ asset('js/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script>
  if (window.tinymce) {
    tinymce.init({
      selector: 'textarea.tinymce-editor[name="content"]',
      height: 400,
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
