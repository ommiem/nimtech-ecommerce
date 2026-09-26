@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Edit Slide</h1>

<form action="{{ route('admin.slides.update', $slide) }}" method="POST" enctype="multipart/form-data" class="bg-white border rounded p-4 max-w-3xl">
  @csrf
  @method('PUT')
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
      <label class="block text-sm">Title</label>
      <input name="title" value="{{ old('title', $slide->title) }}" class="w-full border rounded px-3 py-2" required>
      @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm">Text</label>
      <textarea name="text" rows="3" class="w-full border rounded px-3 py-2">{{ old('text', $slide->text) }}</textarea>
    </div>
    <div>
      <label class="block text-sm">CTA Text</label>
      <input name="cta_text" value="{{ old('cta_text', $slide->cta_text) }}" class="w-full border rounded px-3 py-2">
    </div>
    <div>
      <label class="block text-sm">CTA URL</label>
      <input name="cta_url" value="{{ old('cta_url', $slide->cta_url) }}" class="w-full border rounded px-3 py-2">
    </div>
    <div>
      <label class="block text-sm">Background (Tailwind classes)</label>
      <input name="bg" value="{{ old('bg', $slide->bg) }}" class="w-full border rounded px-3 py-2">
    </div>
    <div>
      <label class="block text-sm">Image (optional)</label>
      <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
      @error('image')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      @if($slide->image)
        <div class="mt-2">
          <img src="{{ asset('storage/'.$slide->image) }}" alt="Current image" class="h-24 object-cover rounded border">
        </div>
      @endif
    </div>
    <div>
      <label class="block text-sm">Active</label>
      <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $slide->is_active)) class="rounded border-gray-300">
    </div>
    <div>
      <label class="block text-sm">Sort Order</label>
      <input type="number" name="sort_order" value="{{ old('sort_order', $slide->sort_order) }}" class="w-full border rounded px-3 py-2">
    </div>
  </div>
  <div class="mt-4 flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
    <a href="{{ route('admin.slides.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
  </div>
</form>
@endsection
