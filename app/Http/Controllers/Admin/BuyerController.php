<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');

        $buyers = User::query()
            ->where('user_type', 'buyer')
            ->withCount('orders')
            ->with([
                'defaultAddress',
                'latestAddress',
                'latestOrder',
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.buyers.index', compact('buyers', 'q'));
    }
}
