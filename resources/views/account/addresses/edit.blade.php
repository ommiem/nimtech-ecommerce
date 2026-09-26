@extends('layouts.app')

@section('content')
<x-breadcrumbs :items="[[ 'label'=>'Home', 'url'=>route('products.index')],[ 'label'=>'Addresses','url'=>route('account.addresses.index') ],[ 'label'=>'Edit Address' ]]" />
<h1 class="text-2xl font-semibold mb-4">Edit Address</h1>
<form action="{{ route('account.addresses.update', $address) }}" method="POST" class="bg-white border rounded p-4 max-w-2xl">
  @method('PUT')
  @include('account.addresses.form', ['address'=>$address])
  <div class="mt-4 space-x-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
    <a href="{{ route('account.addresses.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
  </div>
</form>
@endsection

