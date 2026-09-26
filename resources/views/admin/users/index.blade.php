@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-4">
  <div>
    <h1 class="text-2xl font-semibold">Users</h1>
    <p class="text-sm text-gray-600">Manage admin and buyer accounts.</p>
  </div>
  <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">New User</a>
</div>

@if(session('success'))
  <div class="mb-4 p-3 bg-green-100 text-green-800 border border-green-200 rounded">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="mb-4 p-3 bg-red-100 text-red-800 border border-red-200 rounded">{{ session('error') }}</div>
@endif

<form method="GET" action="{{ route('admin.users.index') }}" class="bg-white border rounded p-3 flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
  <div class="flex-1">
    <label class="sr-only" for="q">Search</label>
    <div class="relative">
      <input id="q" type="text" name="q" value="{{ $q }}" placeholder="Search by name or email..." class="w-full border rounded pl-3 pr-9 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
      <button class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500" aria-label="Search">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 3a7.5 7.5 0 105.236 12.764l3.75 3.75a.75.75 0 101.06-1.06l-3.75-3.75A7.5 7.5 0 0010.5 3z"/></svg>
      </button>
    </div>
  </div>
  <div>
    <label class="sr-only" for="type">Type</label>
    <select id="type" name="type" class="border rounded px-3 py-2 text-sm">
      <option value="all" @selected($type === 'all')>All Types</option>
      <option value="admin" @selected($type === 'admin')>Admins</option>
      <option value="buyer" @selected($type === 'buyer')>Buyers</option>
    </select>
  </div>
  <div>
    <button class="px-3 py-2 border rounded text-sm bg-gray-50 hover:bg-gray-100">Filter</button>
  </div>
</form>

<div class="bg-white border rounded overflow-x-auto">
  <table class="min-w-full">
    <thead>
      <tr class="bg-gray-100 text-left text-sm">
        <th class="p-3 border">User</th>
        <th class="p-3 border">Type</th>
        <th class="p-3 border">Orders</th>
        <th class="p-3 border">Joined</th>
        <th class="p-3 border text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
    @forelse($users as $u)
      @php($isSelf = auth()->id() === $u->id)
      <tr class="text-sm">
        <td class="p-3 border align-top">
          <div class="font-semibold">{{ $u->name }}</div>
          <div class="text-gray-600">{{ $u->email }}</div>
        </td>
        <td class="p-3 border align-top">
          <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $u->user_type === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }}">
            {{ ucfirst($u->user_type ?? 'buyer') }}
          </span>
        </td>
        <td class="p-3 border align-top">{{ $u->orders_count ?? 0 }}</td>
        <td class="p-3 border align-top text-gray-600">{{ optional($u->created_at)->format('Y-m-d') }}</td>
        <td class="p-3 border align-top">
          <div class="flex justify-end items-center gap-2">
            <a href="{{ route('admin.users.edit', $u) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Delete this user?');">
              @csrf
              @method('DELETE')
              <button class="{{ $isSelf ? 'text-gray-400 cursor-not-allowed' : 'text-red-600 hover:underline' }} text-sm" @if($isSelf) disabled title="You cannot delete your own account" @endif>
                Delete
              </button>
            </form>
          </div>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="5" class="p-4 text-center text-gray-500">No users found.</td>
      </tr>
    @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $users->links() }}</div>
@endsection
