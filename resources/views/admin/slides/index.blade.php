@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-semibold">Slides</h1>
  <a href="{{ route('admin.slides.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">New Slide</a>
</div>

<div class="bg-white border rounded">
  <table class="min-w-full">
    <thead>
      <tr class="bg-gray-100 text-left">
        <th class="p-3 border">Preview</th>
        <th class="p-3 border">Title</th>
        <th class="p-3 border">CTA</th>
        <th class="p-3 border">Active</th>
        <th class="p-3 border">Sort</th>
        <th class="p-3 border">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($slides as $slide)
      <tr>
        <td class="p-3 border">
          @if($slide->image)
            <img src="{{ asset('storage/'.$slide->image) }}" class="h-12 w-24 object-cover rounded border" alt="{{ $slide->title ?? 'Slide' }}">
          @else
            <div class="h-12 w-24 bg-gray-100 rounded border"></div>
          @endif
        </td>
        <td class="p-3 border">{{ $slide->title }}</td>
        <td class="p-3 border">{{ $slide->cta_text }} <span class="text-gray-500">{{ $slide->cta_url }}</span></td>
        <td class="p-3 border">{!! $slide->is_active ? '<span class="text-green-700">Yes</span>' : '<span class="text-gray-500">No</span>' !!}</td>
        <td class="p-3 border">{{ $slide->sort_order }}</td>
        <td class="p-3 border space-x-2">
          <a class="text-blue-700 hover:underline" href="{{ route('admin.slides.edit', $slide) }}">Edit</a>
          <form class="inline" action="{{ route('admin.slides.destroy', $slide) }}" method="POST" onsubmit="return confirm('Delete this slide?')">
            @csrf
            @method('DELETE')
            <button class="text-red-700 hover:underline">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $slides->links() }}</div>
@endsection
