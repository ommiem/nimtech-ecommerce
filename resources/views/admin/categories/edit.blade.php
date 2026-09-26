@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Edit Category</h1>

<form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="bg-white border rounded p-4 max-w-2xl">
  @csrf
  @method('PUT')
  <div class="grid grid-cols-1 gap-4">
    <div>
      <label class="block text-sm">Name</label>
      <input name="name" value="{{ old('name', $category->name) }}" class="w-full border rounded px-3 py-2" required>
      @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Slug</label>
      <input name="slug" value="{{ old('slug', $category->slug) }}" class="w-full border rounded px-3 py-2">
      @error('slug')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Parent Category (optional)</label>
      <select name="parent_id" class="w-full border rounded px-3 py-2 bg-white">
        <option value="">-- None --</option>
        @foreach($parents as $parent)
          <option value="{{ $parent->id }}" @selected((string)old('parent_id', $category->parent_id) === (string)$parent->id)>{{ $parent->name }}</option>
        @endforeach
      </select>
      @error('parent_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Description</label>
      <textarea name="description" class="w-full border rounded px-3 py-2" rows="4">{{ old('description', $category->description) }}</textarea>
    </div>
    <div>
      <label class="block text-sm">Image (optional)</label>
      <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
      @error('image')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      @if($category->image_path)
        <div class="mt-2">
          <img src="{{ asset('storage/'.$category->image_path) }}" alt="Current image" class="h-20 object-cover rounded border">
        </div>
      @endif
    </div>
    <div class="flex items-center gap-4">
      <label class="inline-flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $category->is_featured))>
        <span>Featured</span>
      </label>
      <div>
        <label class="block text-sm">Sort Order (optional)</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="w-28 border rounded px-3 py-2">
      </div>
    </div>
  </div>
  <div class="mt-4 flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
  </div>
</form>
@endsection
