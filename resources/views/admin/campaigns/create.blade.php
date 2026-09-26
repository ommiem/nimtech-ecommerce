@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Create Campaign</h1>

<form action="{{ route('admin.campaigns.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border rounded p-4 max-w-5xl">
  @csrf
  @include('admin.campaigns._form')

  <div class="mt-4 flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Create Campaign</button>
    <a href="{{ route('admin.campaigns.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
  </div>
</form>
@endsection
