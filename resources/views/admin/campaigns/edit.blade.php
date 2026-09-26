@extends('layouts.admin')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
  <h1 class="text-2xl font-semibold">Edit Campaign</h1>
  <a href="{{ route('campaigns.show', $campaign) }}" target="_blank" class="px-3 py-1.5 border rounded text-sm hover:bg-gray-50">Open Landing Page</a>
</div>

<form action="{{ route('admin.campaigns.update', $campaign) }}" method="POST" enctype="multipart/form-data" class="bg-white border rounded p-4 max-w-5xl">
  @csrf
  @method('PUT')
  @include('admin.campaigns._form')

  <div class="mt-4 flex flex-wrap gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save Campaign</button>
    <a href="{{ route('admin.campaigns.index') }}" class="px-4 py-2 bg-gray-200 rounded">Back</a>
  </div>
</form>
@endsection
