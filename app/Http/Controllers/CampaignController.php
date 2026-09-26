<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function show(Campaign $campaign): View
    {
        if (! $campaign->published && ! $this->canPreview()) {
            abort(404);
        }

        $campaign->load([
            'promotion',
            'products.category',
            'products.brand',
            'products.images',
        ]);

        $bundleItems = $campaign->products
            ->map(function ($product) {
                $quantity = max(1, (int) ($product->pivot->quantity ?? 1));

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'line_total' => $product->price * $quantity,
                    'highlights' => $this->extractHighlights((string) ($product->description ?? '')),
                ];
            })
            ->values();

        return view('theme::campaigns.show', [
            'campaign' => $campaign,
            'bundleItems' => $bundleItems,
            'bundleTotal' => $bundleItems->sum('line_total'),
            'bundleWhatsApp' => $campaign->whatsapp_message ? whatsapp_link($campaign->whatsapp_message) : null,
        ]);
    }

    public function addBundle(Request $request, Campaign $campaign): RedirectResponse
    {
        if (! $campaign->published && ! $this->canPreview()) {
            abort(404);
        }

        $campaign->load('products');

        $added = 0;
        $skipped = [];

        foreach ($campaign->products as $product) {
            $requested = max(1, (int) ($product->pivot->quantity ?? 1));
            $existing = Cart::all()[$product->id] ?? 0;

            if (is_numeric($product->stock)) {
                $availableToAdd = max(0, (int) $product->stock - (int) $existing);
                if ($availableToAdd <= 0) {
                    $skipped[] = $product->name;
                    continue;
                }

                $requested = min($requested, $availableToAdd);
            }

            Cart::add($product->id, $requested);
            $added++;
        }

        if ($added === 0) {
            return back()->with('error', 'No bundle items could be added because the selected stock is unavailable.');
        }

        $message = $added === 1 ? 'Bundle item added to cart.' : 'Bundle items added to cart.';
        if ($skipped !== []) {
            $message .= ' Some items were skipped due to stock: '.implode(', ', array_slice($skipped, 0, 3)).(count($skipped) > 3 ? '...' : '');
        }

        if ($request->query('redirect') === 'checkout') {
            return redirect()->route('checkout.index')->with('success', $message);
        }

        return back()->with('success', $message);
    }

    private function canPreview(): bool
    {
        return auth()->check() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin();
    }

    private function extractHighlights(string $description): array
    {
        $highlights = [];

        foreach (preg_split('/\r?\n/', $description) as $line) {
            $line = trim(strip_tags($line));
            if ($line === '') {
                continue;
            }

            if (preg_match('/^([\-\*\x{2022}])\s*(.+)$/u', $line, $matches)) {
                $highlights[] = $matches[2];
            }
        }

        if ($highlights === []) {
            $plain = trim(strip_tags($description));
            if ($plain !== '') {
                $highlights[] = Str::limit($plain, 120);
            }
        }

        return array_slice($highlights, 0, 3);
    }
}
