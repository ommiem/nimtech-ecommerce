<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::orderByDesc('id')->paginate(20)->withQueryString();
        return view('admin.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('admin.promotions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promotions,code'],
            'type' => ['required', 'in:percent,amount'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_subtotal' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $data['code'] = Str::upper($data['code']);
        $data['active'] = $request->boolean('active', true);

        Promotion::create($data);

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion created.');
    }

    public function edit(Promotion $promotion)
    {
        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promotions,code,'.$promotion->id],
            'type' => ['required', 'in:percent,amount'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_subtotal' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $data['code'] = Str::upper($data['code']);
        $data['active'] = $request->boolean('active', true);

        $promotion->update($data);

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion updated.');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return back()->with('success', 'Promotion deleted.');
    }
}

