<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::query()
            ->withCount('products')
            ->with('promotion')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('admin.campaigns.create', [
            'campaign' => new Campaign([
                'compare_enabled' => true,
                'published' => true,
            ]),
            'products' => Product::query()->with('category')->orderBy('name')->get(),
            'promotions' => Promotion::query()->orderBy('code')->get(),
            'selectedProducts' => collect(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateCampaign($request);

        DB::transaction(function () use ($data, $request) {
            $campaign = new Campaign();
            $campaign->fill($data);
            $campaign->slug = Campaign::makeUniqueSlug(Str::slug($data['slug'] ?: $data['title']));
            $campaign->published = $request->boolean('published', true);
            $campaign->compare_enabled = $request->boolean('compare_enabled', true);
            $this->storeFeaturedImage($request, $campaign);
            $campaign->save();

            $this->syncProducts($campaign, (array) $request->input('bundle_products', []));
        });

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign created.');
    }

    public function edit(Campaign $campaign)
    {
        $campaign->load('products');

        return view('admin.campaigns.edit', [
            'campaign' => $campaign,
            'products' => Product::query()->with('category')->orderBy('name')->get(),
            'promotions' => Promotion::query()->orderBy('code')->get(),
            'selectedProducts' => $campaign->products->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => (int) ($product->pivot->quantity ?? 1),
                    'sort_order' => (int) ($product->pivot->sort_order ?? 0),
                ];
            })->values(),
        ]);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $data = $this->validateCampaign($request, $campaign);

        DB::transaction(function () use ($campaign, $data, $request) {
            $campaign->fill($data);
            $campaign->slug = Campaign::makeUniqueSlug(Str::slug($data['slug'] ?: $data['title']), $campaign->id);
            $campaign->published = $request->boolean('published', true);
            $campaign->compare_enabled = $request->boolean('compare_enabled', true);

            if ($request->boolean('remove_featured_image') && $campaign->featured_image) {
                Storage::disk('public')->delete($campaign->featured_image);
                $campaign->featured_image = null;
            }

            $this->storeFeaturedImage($request, $campaign);
            $campaign->save();

            $this->syncProducts($campaign, (array) $request->input('bundle_products', []));
        });

        return redirect()->route('admin.campaigns.edit', $campaign)->with('success', 'Campaign updated.');
    }

    public function destroy(Campaign $campaign)
    {
        if ($campaign->featured_image) {
            Storage::disk('public')->delete($campaign->featured_image);
        }

        $campaign->delete();

        return back()->with('success', 'Campaign deleted.');
    }

    private function validateCampaign(Request $request, ?Campaign $campaign = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:campaigns,slug'.($campaign ? ','.$campaign->id : '')],
            'hero_badge' => ['nullable', 'string', 'max:120'],
            'summary' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'image', 'max:4096'],
            'remove_featured_image' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'cta_label' => ['nullable', 'string', 'max:120'],
            'cta_url' => ['nullable', 'string', 'max:2048'],
            'whatsapp_message' => ['nullable', 'string', 'max:2000'],
            'promotion_id' => ['nullable', 'exists:promotions,id'],
            'compare_enabled' => ['sometimes', 'boolean'],
            'published' => ['sometimes', 'boolean'],
            'bundle_products' => ['nullable', 'array'],
            'bundle_products.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
            'bundle_products.*.quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
            'bundle_products.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
    }

    private function storeFeaturedImage(Request $request, Campaign $campaign): void
    {
        if (! $request->hasFile('featured_image')) {
            return;
        }

        if ($campaign->featured_image) {
            Storage::disk('public')->delete($campaign->featured_image);
        }

        $campaign->featured_image = $request->file('featured_image')->store('campaigns', 'public');
    }

    private function syncProducts(Campaign $campaign, array $bundleProducts): void
    {
        $sync = [];

        foreach ($bundleProducts as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            if ($productId <= 0) {
                continue;
            }

            $sync[$productId] = [
                'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
                'sort_order' => max(0, (int) ($item['sort_order'] ?? 0)),
            ];
        }

        $campaign->products()->sync($sync);
    }
}
