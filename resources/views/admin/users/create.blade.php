@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-semibold">New User</h1>
  <a href="{{ route('admin.users.index') }}" class="text-sm px-3 py-1.5 border rounded hover:bg-gray-50">Back to Users</a>
</div>

<form action="{{ route('admin.users.store') }}" method="POST" class="bg-white border rounded p-4 space-y-4 max-w-4xl">
  @csrf

  @include('admin.users.partials.form')

  <div class="pt-2 flex items-center gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Create User</button>
    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
  </div>
</form>
@endsection
