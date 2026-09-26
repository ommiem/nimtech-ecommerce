@extends('layouts.admin')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
  <h1 class="text-2xl font-semibold">WhatsApp Leads</h1>
  <form method="GET" action="{{ route('admin.whatsapp-leads.index') }}" class="flex items-center gap-2">
    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search phone or product" class="border rounded px-3 py-2 text-sm w-64">
    <button class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Search</button>
    @if(request('q'))
      <a href="{{ route('admin.whatsapp-leads.index') }}" class="text-sm text-gray-600">Clear</a>
    @endif
  </form>
</div>

<div class="bg-white border rounded">
  <table class="min-w-full text-sm">
    <thead>
      <tr class="bg-gray-100 text-left">
        <th class="p-3 border">#</th>
        <th class="p-3 border">Phone</th>
        <th class="p-3 border">Product</th>
        <th class="p-3 border">Message</th>
        <th class="p-3 border">User</th>
        <th class="p-3 border">Created</th>
      </tr>
    </thead>
    <tbody>
      @forelse($leads as $lead)
        <tr>
          <td class="p-3 border">{{ $lead->id }}</td>
          <td class="p-3 border">
            <a class="text-blue-700 hover:underline" href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a>
            @if($lead->ip_address)
              <div class="text-xs text-gray-500">IP: {{ $lead->ip_address }}</div>
            @endif
          </td>
          <td class="p-3 border">
            @if($lead->product_url)
              <a class="text-blue-700 hover:underline" href="{{ $lead->product_url }}" target="_blank" rel="noopener">
                {{ $lead->product_name ?? 'View product' }}
              </a>
            @else
              {{ $lead->product_name ?? '-' }}
            @endif
            @if($lead->product_id)
              <div class="text-xs text-gray-500">Product ID: {{ $lead->product_id }}</div>
            @endif
          </td>
          <td class="p-3 border">
            <div class="text-gray-700">{{ Str::limit($lead->message ?? '', 120) }}</div>
          </td>
          <td class="p-3 border">
            @if($lead->user)
              {{ $lead->user->name }}
              <div class="text-xs text-gray-500">{{ $lead->user->email }}</div>
            @else
              <span class="text-xs text-gray-500">Guest</span>
            @endif
          </td>
          <td class="p-3 border">
            {{ $lead->created_at?->format('M j, Y g:i A') ?? '-' }}
          </td>
        </tr>
      @empty
        <tr>
          <td class="p-3 border text-center text-gray-500" colspan="6">No leads yet.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $leads->links() }}</div>
@endsection
