<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappLead;
use Illuminate\Http\Request;

class WhatsappLeadController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');

        $leads = WhatsappLead::query()
            ->with(['product', 'user'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('phone', 'like', "%{$q}%")
                        ->orWhere('product_name', 'like', "%{$q}%")
                        ->orWhere('product_url', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.whatsapp-leads.index', compact('leads', 'q'));
    }
}
