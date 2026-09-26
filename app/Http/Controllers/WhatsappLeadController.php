<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\WhatsappLead;
use Illuminate\Http\Request;

class WhatsappLeadController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:32'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'product_name' => ['nullable', 'string', 'max:255'],
            'product_url' => ['nullable', 'string', 'max:2048'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['phone'] = trim($data['phone']);

        $product = null;
        if (!empty($data['product_id'])) {
            $product = Product::find($data['product_id']);
        }

        if (empty($data['product_name']) && $product) {
            $data['product_name'] = $product->name;
        }

        if (empty($data['product_url']) && $product) {
            $data['product_url'] = route('products.show', ['productSlug' => $product->canonical_slug]);
        }

        WhatsappLead::create([
            'user_id' => $request->user()?->id,
            'product_id' => $data['product_id'] ?? null,
            'product_name' => $data['product_name'] ?? null,
            'product_url' => $data['product_url'] ?? null,
            'phone' => $data['phone'],
            'message' => $data['message'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
        ]);

        return response()->json(['ok' => true]);
    }
}
