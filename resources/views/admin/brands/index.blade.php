@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-semibold">Brands</h1>
  <a href="{{ route('admin.brands.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">New Brand</a>
  </div>

<div class="bg-white border rounded">
  <table class="min-w-full">
    <thead>
      <tr class="bg-gray-100 text-left">
        <th class="p-3 border">Logo</th>
        <th class="p-3 border">Name</th>
        <th class="p-3 border">Website</th>
        <th class="p-3 border">Active</th>
        <th class="p-3 border">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($brands as $b)
        <tr>
          <td class="p-3 border">@if($b->image_path)<img src="{{ asset('storage/'.$b->image_path) }}" class="h-8" alt="{{ $b->name }}">@endif</td>
          <td class="p-3 border">{{ $b->name }}</td>
          <td class="p-3 border text-sm text-blue-700">@if($b->website)<a target="_blank" href="{{ $b->website }}">{{ $b->website }}</a>@endif</td>
          <td class="p-3 border">{{ $b->active ? 'Yes' : 'No' }}</td>
          <td class="p-3 border space-x-2">
            <a class="text-blue-700 hover:underline" href="{{ route('admin.brands.edit', $b) }}">Edit</a>
            <form class="inline" method="POST" action="{{ route('admin.brands.destroy', $b) }}" onsubmit="return confirm('Delete this brand?')">
              @csrf @method('DELETE')
              <button class="text-red-700 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $brands->links() }}</div>
@endsection
