@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-semibold">Pages</h1>
  <a href="{{ route('admin.pages.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">New Page</a>
</div>

<div class="bg-white border rounded">
  <table class="min-w-full">
    <thead>
      <tr class="bg-gray-100 text-left">
        <th class="p-3 border">Title</th>
        <th class="p-3 border">Featured Image</th>
        <th class="p-3 border">Slug</th>
        <th class="p-3 border">Published</th>
        <th class="p-3 border">Actions</th>
      </tr>
    </thead>
    <tbody>
    @foreach($pages as $p)
      <tr>
        <td class="p-3 border">{{ $p->title }}</td>
        <td class="p-3 border">
          @if($p->featured_image)
            <img src="{{ asset('storage/'.$p->featured_image) }}" alt="Featured image" class="h-12 w-12 object-cover rounded border">
          @else
            <span class="text-gray-400 text-sm">No image</span>
          @endif
        </td>
        <td class="p-3 border text-gray-600">{{ $p->slug }}</td>
        <td class="p-3 border">{!! $p->published ? '<span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-700">Yes</span>' : '<span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-700">No</span>' !!}</td>
        <td class="p-3 border space-x-2">
          <a class="text-blue-700 hover:underline" href="{{ route('admin.pages.edit', $p) }}">Edit</a>
          <a class="text-gray-700 hover:underline" target="_blank" href="{{ route('pages.show', $p) }}">View</a>
          <form class="inline" method="POST" action="{{ route('admin.pages.destroy', $p) }}" onsubmit="return confirm('Delete this page?')">
            @csrf @method('DELETE')
            <button class="text-red-700 hover:underline">Delete</button>
          </form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $pages->links() }}</div>
@endsection
