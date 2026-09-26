@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-semibold mb-4">M-Pesa Tools</h1>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="bg-white border rounded p-4">
    <h2 class="text-lg font-semibold mb-3">Paybill Balance</h2>
    <p class="text-sm text-gray-600 mb-3">
      Check the current balance of your configured M-Pesa paybill or till.
    </p>
    <form action="{{ route('admin.mpesa.tools.balance') }}" method="POST" class="space-y-3">
      @csrf
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Check Balance</button>
    </form>
    @if($balance)
      <div class="mt-4 text-sm">
        <div class="font-semibold mb-1">Last Result</div>
        <pre class="text-xs bg-gray-50 border rounded p-2 overflow-auto">{{ json_encode($balance, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
      </div>
    @endif
  </div>

  <div class="bg-white border rounded p-4">
    <h2 class="text-lg font-semibold mb-3">B2C Payment</h2>
    <p class="text-sm text-gray-600 mb-3">
      Send a Business-to-Customer payout to a phone number (e.g. refunds, commissions).
    </p>
    <form action="{{ route('admin.mpesa.tools.b2c') }}" method="POST" class="space-y-3">
      @csrf
      <div>
        <label class="block text-sm">Phone</label>
        <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2" placeholder="07XXXXXXXX or 2547XXXXXXXX" required>
        @error('phone')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>
      <div>
        <label class="block text-sm">Amount</label>
        <input type="number" step="0.01" min="1" name="amount" value="{{ old('amount') }}" class="w-full border rounded px-3 py-2" required>
        @error('amount')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>
      <div>
        <label class="block text-sm">Reference</label>
        <input type="text" name="reference" value="{{ old('reference') }}" class="w-full border rounded px-3 py-2" required>
        @error('reference')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Send B2C Payment</button>
    </form>
    @if($b2cResult)
      <div class="mt-4 text-sm">
        <div class="font-semibold mb-1">Last Result</div>
        <pre class="text-xs bg-gray-50 border rounded p-2 overflow-auto">{{ json_encode($b2cResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
      </div>
    @endif
  </div>
</div>
@endsection

