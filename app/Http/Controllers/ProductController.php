<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSlugRedirect;
use App\Models\Setting;
use App\Models\Slide;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, ?string $category = null)
    {
        $requestedCategorySlug = $category;
        if (!$requestedCategorySlug && $request->filled('category')) {
            $requestedCategorySlug = $request->query('category');
        }

        $currentCategory = null;
        if ($requestedCategorySlug) {
            $currentCategory = Category::findByPublicSlug($requestedCategorySlug);
            if (!$currentCategory) {
                abort(404);
            }

            $categorySlug = $currentCategory->canonical_slug;
            if ($request->filled('category') || $requestedCategorySlug !== $categorySlug) {
                $params = $request->except('category');

                return redirect()->route('categories.show', array_merge(['category' => $categorySlug], $params), 301);
            }
        }

        $categorySlug = $currentCategory?->canonical_slug;
        $brandSlug = $request->query('brand');
        $q = $request->query('q');
        $sort = $request->query('sort', 'latest');
        $priceMin = $request->query('price_min');
        $priceMax = $request->query('price_max');
        $perPage = (int) $request->query('per_page', 12);
        if ($perPage < 6) $perPage = 6; if ($perPage > 60) $perPage = 60;

        $query = Product::query()->with('category');
        if ($currentCategory) {
            $query->where('category_id', $currentCategory->id);
        }
        if ($brandSlug) {
            $brand = \App\Models\Brand::where('slug', $brandSlug)->first();
            if ($brand) { $query->where('brand_id', $brand->id); }
        }
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('slug', 'like', "%$q%");
            });
        }
        if (is_numeric($priceMin)) { $query->where('price', '>=', (float)$priceMin); }
        if (is_numeric($priceMax)) { $query->where('price', '<=', (float)$priceMax); }

        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'latest':
            default:
                $query->orderByDesc('id');
        }

        $products = $query->paginate($perPage)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $brands = \App\Models\Brand::orderBy('name')->get();
        $slides = Slide::active()->orderBy('sort_order')->get();

        return view('theme::products.index', compact('products', 'categories', 'brands', 'categorySlug', 'currentCategory', 'brandSlug', 'q', 'slides', 'sort', 'priceMin', 'priceMax', 'perPage'));
    }

    public function show(Request $request, string $productSlug)
    {
        $product = Product::findByPublicSlug($productSlug);
        if (! $product) {
            $redirect = ProductSlugRedirect::findByPublicSlug($productSlug);

            if ($redirect?->product && ! $redirect->product->trashed()) {
                return redirect()->route('products.show', ['productSlug' => $redirect->product->canonical_slug], 301);
            }

            if ($redirect?->category) {
                return redirect()->route('categories.show', ['category' => $redirect->category->canonical_slug], 301);
            }

            $trashedProduct = Product::findByPublicSlug($productSlug, true);
            if ($trashedProduct?->trashed() && $trashedProduct->category) {
                ProductSlugRedirect::rememberCategoryFallback($productSlug, $trashedProduct->category);

                return redirect()->route('categories.show', ['category' => $trashedProduct->category->canonical_slug], 301);
            }

            abort(404);
        }

        if (trim((string) $productSlug) !== $product->canonical_slug) {
            return redirect()->route('products.show', ['productSlug' => $product->canonical_slug], 301);
        }

        $product->load(['category','images','brand','priceHistory']);
        $settings = Setting::getCached();
        $imagePaths = [];
        if ($product->image) { $imagePaths[] = $product->image; }
        foreach ($product->images as $img) { $imagePaths[] = $img->path; }
        // Prefer .webp files if present on disk
        $imagePaths = array_map(function ($p) {
            $public = storage_path('app/public/');
            $webp = preg_replace('/\.[^.]+$/', '.webp', $p);
            return ($webp && is_file($public.$webp)) ? $webp : $p;
        }, $imagePaths);
        $highlights = [];
        $desc = (string) ($product->description ?? '');
        foreach (preg_split('/\r?\n/', $desc) as $line) {
            $t = trim($line);
            if ($t === '') continue;
            if (preg_match('/^([\-\*•])\s*(.+)$/', $t, $m)) { $highlights[] = $m[2]; }
        }
        if (empty($highlights)) { $highlights = ['Premium quality','Fast delivery across Kenya','Secure checkout']; }
        $currencyCode = strtoupper((string) ($settings?->currency_code ?? 'USD'));
        $shippingRate = max(0, (float) ($settings?->shipping_flat_rate ?? 0));
        $brandName = trim((string) ($product->brand?->name ?? ''));
        $recentPriceHistory = $product->priceHistory->where('recorded_at', '>=', now()->subDays(30));
        $thirtyDayLow = $recentPriceHistory->min('price');
        $conditionText = strtolower($product->name.' '.strip_tags((string) $product->description));
        $itemCondition = preg_match('/refurbished|ex[\s-]?uk|pre[\s-]?owned|used/', $conditionText)
            ? 'RefurbishedCondition'
            : 'NewCondition';
        // JSON-LD payload prepared server-side to avoid Blade conditionals in script blocks
        $jsonLd = array_filter([
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => array_map(fn ($p) => asset('storage/'.$p), $imagePaths),
            'description' => strip_tags($product->description ?? ''),
            'category' => optional($product->category)->name,
            'sku' => (string) $product->id,
            'url' => route('products.show', ['productSlug' => $product->canonical_slug]),
            'brand' => $brandName !== '' ? [
                '@type' => 'Brand',
                'name' => $brandName,
            ] : null,
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => $currencyCode,
                'price' => number_format((float) $product->price, 2, '.', ''),
                'availability' => 'https://schema.org/'.($product->stock > 0 ? 'InStock' : 'OutOfStock'),
                'itemCondition' => 'https://schema.org/'.$itemCondition,
                'url' => route('products.show', ['productSlug' => $product->canonical_slug]),
                'shippingDetails' => [
                    '@type' => 'OfferShippingDetails',
                    'shippingRate' => [
                        '@type' => 'MonetaryAmount',
                        'value' => number_format($shippingRate, 2, '.', ''),
                        'currency' => $currencyCode,
                    ],
                    'shippingDestination' => [
                        '@type' => 'DefinedRegion',
                        'addressCountry' => 'KE',
                    ],
                    'deliveryTime' => [
                        '@type' => 'ShippingDeliveryTime',
                        'handlingTime' => [
                            '@type' => 'QuantitativeValue',
                            'minValue' => 0,
                            'maxValue' => 1,
                            'unitCode' => 'DAY',
                        ],
                        'transitTime' => [
                            '@type' => 'QuantitativeValue',
                            'minValue' => 1,
                            'maxValue' => 3,
                            'unitCode' => 'DAY',
                        ],
                    ],
                ],
                'hasMerchantReturnPolicy' => [
                    '@type' => 'MerchantReturnPolicy',
                    'applicableCountry' => 'KE',
                    'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                    'merchantReturnDays' => 7,
                    'returnMethod' => 'https://schema.org/ReturnByMail',
                    'returnFees' => 'https://schema.org/FreeReturn',
                ],
            ],
        ], fn ($value) => !is_null($value));
        $related = \App\Models\Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest('id')
            ->take(8)
            ->get();
        return view('theme::products.show', compact('product','related','imagePaths','highlights','jsonLd','itemCondition','recentPriceHistory','thirtyDayLow'));
    }
}
