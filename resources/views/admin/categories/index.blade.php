@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">New Category</a>
  </div>

@if(session('error'))
  <div class="mb-4 p-2 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
@endif

@if(session('deleted_category_id'))
  <div class="mb-3 p-3 bg-yellow-100 text-yellow-900 rounded">
    Category deleted.
    <form action="{{ route('admin.categories.restore', session('deleted_category_id')) }}" method="POST" class="inline">
      @csrf
      <button class="underline font-semibold">Undo</button>
    </form>
  </div>
@endif

<div class="mb-3 space-x-2">
  <a href="{{ route('admin.categories.index') }}" class="px-2 py-1 border rounded {{ request('trashed') ? '' : 'bg-gray-200' }}">Active</a>
  <a href="{{ route('admin.categories.index', ['trashed' => 1]) }}" class="px-2 py-1 border rounded {{ request('trashed') ? 'bg-gray-200' : '' }}">Trashed</a>
</div>

<div class="bg-white border rounded">
  <table class="min-w-full">
    <thead>
      <tr class="bg-gray-100 text-left">
        <th class="p-3 border">Name</th>
        <th class="p-3 border">Slug</th>
        <th class="p-3 border">Products</th>
        <th class="p-3 border">Actions</th>
      </tr>
    </thead>
    <tbody>
    @if($isTrashed ?? false)
      @foreach($categories as $category)
        <tr>
          <td class="p-3 border">{{ $category->name }} @if(method_exists($category,'trashed') && $category->trashed()) <span class="ml-1 text-xs text-gray-500">(trashed)</span>@endif</td>
          <td class="p-3 border text-gray-600">{{ $category->slug }}</td>
          <td class="p-3 border">{{ $category->products()->withTrashed()->count() }}</td>
          <td class="p-3 border space-x-2">
            <form class="inline" action="{{ route('admin.categories.restore', $category->id) }}" method="POST">
              @csrf
              <button class="text-green-700 hover:underline">Restore</button>
            </form>
            <form class="inline" action="{{ route('admin.categories.force-delete', $category->id) }}" method="POST" onsubmit="return confirm('Permanently delete this category? It must have no products.')">
              @csrf
              @method('DELETE')
              <button class="text-red-700 hover:underline">Delete Permanently</button>
            </form>
          </td>
        </tr>
      @endforeach
    @else
      @forelse($parents as $parent)
        <tr class="bg-gray-50">
          <td class="p-3 border font-semibold">{{ $parent->name }}</td>
          <td class="p-3 border text-gray-600">{{ $parent->slug }}</td>
          <td class="p-3 border">{{ $parent->products_count ?? 0 }}</td>
          <td class="p-3 border space-x-2">
            <a class="text-blue-700 hover:underline" href="{{ route('admin.categories.edit', $parent) }}">Edit</a>
            <form class="inline" action="{{ route('admin.categories.destroy', $parent) }}" method="POST" onsubmit="return confirm('Move this category to trash? Associated products will also be trashed.')">
              @csrf
              @method('DELETE')
              <button class="text-red-700 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
        @foreach($parent->children as $child)
          <tr>
            <td class="p-3 border pl-8 text-gray-800">&mdash; {{ $child->name }}</td>
            <td class="p-3 border text-gray-600">{{ $child->slug }}</td>
            <td class="p-3 border">{{ $child->products_count ?? 0 }}</td>
            <td class="p-3 border space-x-2">
              <a class="text-blue-700 hover:underline" href="{{ route('admin.categories.edit', $child) }}">Edit</a>
              <form class="inline" action="{{ route('admin.categories.destroy', $child) }}" method="POST" onsubmit="return confirm('Move this category to trash? Associated products will also be trashed.')">
                @csrf
                @method('DELETE')
                <button class="text-red-700 hover:underline">Delete</button>
              </form>
            </td>
          </tr>
        @endforeach
      @empty
        <tr>
          <td colspan="4" class="p-6 text-gray-600">No categories yet.</td>
        </tr>
      @endforelse
    @endif
    </tbody>
  </table>
</div>

@if($isTrashed ?? false)
  <div class="mt-4">{{ $categories->links() }}</div>
@endif
@endsection
