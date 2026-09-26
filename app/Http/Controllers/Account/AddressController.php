<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = Address::where('user_id', $request->user()->id)->orderByDesc('is_default')->orderByDesc('id')->get();
        return view('account.addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('account.addresses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => ['nullable','string','max:100'],
            'full_name' => ['required','string','max:255'],
            'email' => ['nullable','email'],
            'phone' => ['nullable','string','max:20'],
            'address' => ['required','string','max:255'],
            'city' => ['nullable','string','max:255'],
            'state' => ['nullable','string','max:255'],
            'postal_code' => ['nullable','string','max:50'],
            'is_default' => ['sometimes','boolean'],
        ]);
        $data['user_id'] = $request->user()->id;
        $addr = Address::create($data);
        if ($request->boolean('is_default')) {
            Address::where('user_id', $request->user()->id)->where('id','!=',$addr->id)->update(['is_default'=>false]);
        }
        return redirect()->route('account.addresses.index')->with('success','Address saved.');
    }

    public function edit(Address $address)
    {
        $this->authorizeAddress($address);
        return view('account.addresses.edit', compact('address'));
    }

    public function update(Request $request, Address $address)
    {
        $this->authorizeAddress($address);
        $data = $request->validate([
            'label' => ['nullable','string','max:100'],
            'full_name' => ['required','string','max:255'],
            'email' => ['nullable','email'],
            'phone' => ['nullable','string','max:20'],
            'address' => ['required','string','max:255'],
            'city' => ['nullable','string','max:255'],
            'state' => ['nullable','string','max:255'],
            'postal_code' => ['nullable','string','max:50'],
            'is_default' => ['sometimes','boolean'],
        ]);
        $address->update($data);
        if ($request->boolean('is_default')) {
            Address::where('user_id', $request->user()->id)->where('id','!=',$address->id)->update(['is_default'=>false]);
        }
        return redirect()->route('account.addresses.index')->with('success','Address updated.');
    }

    public function destroy(Request $request, Address $address)
    {
        $this->authorizeAddress($address);
        $address->delete();
        return back()->with('success','Address removed.');
    }

    protected function authorizeAddress(Address $address): void
    {
        abort_unless($address->user_id === auth()->id(), 403);
    }
}

