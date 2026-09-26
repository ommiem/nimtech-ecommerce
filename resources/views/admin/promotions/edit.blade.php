@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Edit Promotion</h1>

<form action="{{ route('admin.promotions.update', $promotion) }}" method="POST" class="bg-white border rounded p-4 max-w-2xl">
  @csrf
  @method('PUT')
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm">Code</label>
      <input name="code" value="{{ old('code', $promotion->code) }}" class="w-full border rounded px-3 py-2" required>
      @error('code')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Type</label>
      <select name="type" class="w-full border rounded px-3 py-2" required>
        <option value="percent" @selected(old('type', $promotion->type)==='percent')>Percent (%)</option>
        <option value="amount" @selected(old('type', $promotion->type)==='amount')>Fixed Amount</option>
      </select>
      @error('type')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Value</label>
      <input type="number" step="0.01" min="0" name="value" value="{{ old('value', $promotion->value) }}" class="w-full border rounded px-3 py-2" required>
      @error('value')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Minimum Subtotal (optional)</label>
      <input type="number" step="0.01" min="0" name="min_subtotal" value="{{ old('min_subtotal', $promotion->min_subtotal) }}" class="w-full border rounded px-3 py-2">
      @error('min_subtotal')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Usage Limit (optional)</label>
      <input type="number" min="0" name="usage_limit" value="{{ old('usage_limit', $promotion->usage_limit) }}" class="w-full border rounded px-3 py-2">
      @error('usage_limit')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Active</label>
      <label class="inline-flex items-center gap-2 text-sm mt-1">
        <input type="checkbox" name="active" value="1" @checked(old('active', $promotion->active))>
        <span>Enabled</span>
      </label>
      @error('active')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Starts At (optional)</label>
      <input type="date" name="starts_at" value="{{ old('starts_at', optional($promotion->starts_at)->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
      @error('starts_at')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm">Ends At (optional)</label>
      <input type="date" name="ends_at" value="{{ old('ends_at', optional($promotion->ends_at)->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
      @error('ends_at')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="mt-4 space-x-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
    <a href="{{ route('admin.promotions.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
  </div>
</form>
@endsection

