@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between gap-3 mb-4">
  <h1 class="text-2xl font-semibold">Campaigns</h1>
  <a href="{{ route('admin.campaigns.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">New Campaign</a>
</div>

<div class="bg-white border rounded overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 text-gray-600">
      <tr>
        <th class="text-left px-4 py-3">Campaign</th>
        <th class="text-left px-4 py-3">Products</th>
        <th class="text-left px-4 py-3">Promotion</th>
        <th class="text-left px-4 py-3">Status</th>
        <th class="text-right px-4 py-3">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($campaigns as $campaign)
        <tr class="border-t">
          <td class="px-4 py-3">
            <div class="font-medium">{{ $campaign->title }}</div>
            <div class="text-xs text-gray-500">/{{ ltrim(parse_url(route('campaigns.show', $campaign), PHP_URL_PATH), '/') }}</div>
          </td>
          <td class="px-4 py-3">{{ $campaign->products_count }}</td>
          <td class="px-4 py-3">{{ $campaign->promotion?->code ?? 'None' }}</td>
          <td class="px-4 py-3">
            <span class="inline-flex rounded-full px-2 py-1 text-xs {{ $campaign->published ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
              {{ $campaign->published ? 'Published' : 'Draft' }}
            </span>
          </td>
          <td class="px-4 py-3">
            <div class="flex justify-end gap-2">
              <a href="{{ route('campaigns.show', $campaign) }}" target="_blank" class="px-3 py-1.5 border rounded">View</a>
              <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="px-3 py-1.5 border rounded">Edit</a>
              <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Delete this campaign?')">
                @csrf
                @method('DELETE')
                <button class="px-3 py-1.5 bg-red-600 text-white rounded">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="px-4 py-8 text-center text-gray-500">No campaigns yet.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $campaigns->links() }}
</div>
@endsection
