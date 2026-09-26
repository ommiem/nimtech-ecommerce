@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-semibold mb-4">New Brand</h1>

<form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border rounded p-4 max-w-2xl">
  @csrf
  <div class="grid grid-cols-1 gap-4">
    <div>
      <label class="block text-sm">Name</label>
      <input name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>
      @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Slug (optional)</label>
      <input name="slug" value="{{ old('slug') }}" class="w-full border rounded px-3 py-2">
      @error('slug')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Website (optional)</label>
      <input name="website" value="{{ old('website') }}" class="w-full border rounded px-3 py-2" type="url">
      @error('website')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Logo (optional)</label>
      <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
      @error('image')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="active" value="1" checked> <span>Active</span></label>
  </div>
  <div class="mt-4 space-x-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
    <a href="{{ route('admin.brands.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
  </div>
</form>
@endsection

