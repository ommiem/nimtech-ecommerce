<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthorityPageController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\SlideController as AdminSlideController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PromotionController as AdminPromotionController;
use App\Http\Controllers\Admin\CampaignController as AdminCampaignController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\WhatsappLeadController as AdminWhatsappLeadController;
use App\Models\Setting;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\BrandController as PublicBrandController;
use App\Http\Controllers\Admin\BuyerController as AdminBuyerController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\Payments\MpesaController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\PageController as PublicPageController;
use App\Http\Controllers\Admin\MpesaToolsController as AdminMpesaToolsController;
use App\Http\Controllers\WhatsappLeadController;
use App\Http\Controllers\SeoCatalogController;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/category/{category}', [ProductController::class, 'index'])->name('categories.show');
Route::get('/product-category/{legacyCategory}', function (string $legacyCategory) {
    $legacyCategory = trim($legacyCategory, '/');
    $category = Category::findByPublicSlug($legacyCategory)
        ?? Category::findByPublicSlug(Str::plural($legacyCategory));

    abort_unless($category, 404);

    return redirect()->route('categories.show', ['category' => $category->canonical_slug], 301);
})->where('legacyCategory', '.*');
Route::get('/products/{productSlug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/phones', [AuthorityPageController::class, 'phones'])->name('seo.phones');
Route::get('/laptops', [AuthorityPageController::class, 'laptops'])->name('seo.laptops');
Route::get('/iphone-kenya', [AuthorityPageController::class, 'iphoneKenya'])->name('seo.iphone-kenya');
Route::get('/samsung-kenya', [AuthorityPageController::class, 'samsungKenya'])->name('seo.samsung-kenya');
Route::get('/hp-laptops-kenya', [AuthorityPageController::class, 'hpLaptopsKenya'])->name('seo.hp-laptops-kenya');
Route::get('/dell-laptops-kenya', [AuthorityPageController::class, 'dellLaptopsKenya'])->name('seo.dell-laptops-kenya');
Route::get('/lenovo-laptops-kenya', [AuthorityPageController::class, 'lenovoLaptopsKenya'])->name('seo.lenovo-laptops-kenya');
Route::get('/xiaomi-phones-kenya', [AuthorityPageController::class, 'xiaomiPhonesKenya'])->name('seo.xiaomi-phones-kenya');
Route::get('/recently-updated-prices', [SeoCatalogController::class, 'updated'])->name('seo.updated');
Route::get('/compare-products', [SeoCatalogController::class, 'compare'])->name('seo.compare');
Route::get('/{type}-under-{amount}-in-kenya', [SeoCatalogController::class, 'budget'])
    ->whereIn('type', ['phones', 'laptops'])->whereNumber('amount')->name('seo.budget');
Route::get('/{slug}', [SeoCatalogController::class, 'landing'])
    ->whereIn('slug', array_keys(SeoCatalogController::LANDINGS))->name('seo.catalog');
Route::get('/campaigns/{campaign:slug}', [CampaignController::class, 'show'])->name('campaigns.show');
Route::post('/campaigns/{campaign:slug}/bundle/add', [CampaignController::class, 'addBundle'])->name('campaigns.bundle.add');
// Public pages at root (no /p prefix). Keep after other top-level routes.
// Prevent conflicts with reserved prefixes via negative lookahead and slug pattern.
// Will match e.g. /about-us or /best-laptop-brands-in-kenya

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
    Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/orders/{order}/success', [CheckoutController::class, 'success'])->name('orders.success');

// WhatsApp leads (public)
Route::post('/whatsapp-leads', [WhatsappLeadController::class, 'store'])->name('whatsapp.leads.store');

// Deals page (applies max price filter)
Route::get('/deals', function () {
    $threshold = optional(Setting::getCached())->deals_under_threshold ?? 5000;
    return redirect()->route('products.index', ['price_max' => $threshold, 'sort' => 'price_asc']);
})->name('deals.index');

// Brands directory
Route::get('/brands', [PublicBrandController::class, 'index'])->name('brands.index');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('account.orders.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('buyers', [AdminBuyerController::class, 'index'])->name('buyers.index');
    Route::resource('pages', AdminPageController::class)->except(['show']);
    Route::resource('brands', AdminBrandController::class)->except(['show']);
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::post('categories/{category}/restore', [AdminCategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('categories/{category}/force-delete', [AdminCategoryController::class, 'forceDelete'])->name('categories.force-delete');
    Route::resource('products', AdminProductController::class);
    Route::post('products/{product}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
    Route::delete('products/{product}/force-delete', [AdminProductController::class, 'forceDelete'])->name('products.force-delete');
    Route::delete('products/{product}/images/{image}', [AdminProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::middleware('admin.super')->group(function () {
        Route::resource('users', AdminUserController::class)->except(['show']);
        Route::get('settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::match(['post', 'put'], 'settings/header-tiles/{tile}', [AdminSettingController::class, 'updateHeaderTile'])->whereNumber('tile')->name('settings.header-tiles.update');
        Route::delete('settings/header-tiles/{tile}', [AdminSettingController::class, 'destroyHeaderTile'])->whereNumber('tile')->name('settings.header-tiles.destroy');
        Route::get('mpesa/tools', [AdminMpesaToolsController::class, 'index'])->name('mpesa.tools');
        Route::post('mpesa/tools/balance', [AdminMpesaToolsController::class, 'balance'])->name('mpesa.tools.balance');
        Route::post('mpesa/tools/b2c', [AdminMpesaToolsController::class, 'b2c'])->name('mpesa.tools.b2c');
    });
    Route::resource('slides', AdminSlideController::class)->except(['show']);
    Route::resource('promotions', AdminPromotionController::class)->except(['show']);
    Route::resource('campaigns', AdminCampaignController::class)->except(['show']);
    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/mark-paid', [AdminOrderController::class, 'markPaid'])->name('orders.mark-paid');
    Route::post('orders/{order}/mark-awaiting', [AdminOrderController::class, 'markAwaitingDelivery'])->name('orders.mark-awaiting');
    Route::post('orders/{order}/mark-pending', [AdminOrderController::class, 'markPending'])->name('orders.mark-pending');
    Route::post('orders/{order}/mark-processing', [AdminOrderController::class, 'markProcessing'])->name('orders.mark-processing');
    Route::post('orders/{order}/mark-shipped', [AdminOrderController::class, 'markShipped'])->name('orders.mark-shipped');
    Route::post('orders/{order}/mark-delivered', [AdminOrderController::class, 'markDelivered'])->name('orders.mark-delivered');
    Route::post('orders/{order}/mark-failed', [AdminOrderController::class, 'markFailed'])->name('orders.mark-failed');
    Route::post('orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('whatsapp-leads', [AdminWhatsappLeadController::class, 'index'])->name('whatsapp-leads.index');
});

// MPESA callbacks (public endpoint expected by Safaricom)
Route::post('/mpesa/stk/callback', [MpesaController::class, 'stkCallback'])->name('mpesa.stk.callback');

// Buyer account routes
Route::middleware(['auth'])->prefix('account')->as('account.')->group(function () {
    Route::get('/', function () { return redirect()->route('account.orders.index'); })->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\Account\ProfileController::class, 'index'])->name('profile');
    Route::get('/addresses', [\App\Http\Controllers\Account\AddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/create', [\App\Http\Controllers\Account\AddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [\App\Http\Controllers\Account\AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [\App\Http\Controllers\Account\AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/addresses/{address}', [\App\Http\Controllers\Account\AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [\App\Http\Controllers\Account\AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::get('/orders', [AccountOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AccountOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/retry', [AccountOrderController::class, 'retryPayment'])->name('orders.retry');
    Route::post('/orders/{order}/pay-now', [AccountOrderController::class, 'payNow'])->name('orders.paynow');
});

// Lightweight status endpoint for polling
Route::get('/orders/{order}/status', function (\App\Models\Order $order) {
    return response()->json([
        'status' => $order->status,
        'paid_at' => optional($order->paid_at)->toIso8601String(),
        'mpesa_receipt' => $order->mpesa_receipt,
        'mpesa_result_code' => $order->mpesa_result_code,
        'mpesa_result_desc' => $order->mpesa_result_desc,
    ]);
})->name('orders.status');

// Sitemap
Route::get('/sitemap.xml', function () {
    $urls = [];
    // Home first
    if (Route::has('home')) {
        $urls[] = [
            'loc' => route('home'),
            'lastmod' => now()->toAtomString(),
        ];
    }
    // Catalog index
    $urls[] = [
        'loc' => route('products.index'),
        'lastmod' => now()->toAtomString(),
    ];
    foreach ([
        'seo.phones',
        'seo.laptops',
        'seo.iphone-kenya',
        'seo.samsung-kenya',
        'seo.hp-laptops-kenya',
        'seo.dell-laptops-kenya',
        'seo.lenovo-laptops-kenya',
        'seo.xiaomi-phones-kenya',
    ] as $authorityRoute) {
        if (Route::has($authorityRoute)) {
            $urls[] = [
                'loc' => route($authorityRoute),
                'lastmod' => now()->toAtomString(),
            ];
        }
    }
    foreach (\App\Http\Controllers\SeoCatalogController::LANDINGS as $slug => $definition) {
        $urls[] = ['loc' => route('seo.catalog', $slug), 'lastmod' => now()->toAtomString()];
    }
    foreach ([
        'phones' => \App\Http\Controllers\SeoCatalogController::PHONE_BUDGETS,
        'laptops' => \App\Http\Controllers\SeoCatalogController::LAPTOP_BUDGETS,
    ] as $type => $amounts) {
        foreach ($amounts as $amount) {
            $urls[] = ['loc' => route('seo.budget', compact('type', 'amount')), 'lastmod' => now()->toAtomString()];
        }
    }
    $urls[] = ['loc' => route('seo.updated'), 'lastmod' => now()->toAtomString()];
    $urls[] = ['loc' => route('seo.compare'), 'lastmod' => now()->toAtomString()];
    foreach (\App\Models\Campaign::query()->where('published', true)->orderByDesc('updated_at')->get() as $campaign) {
        $urls[] = [
            'loc' => route('campaigns.show', $campaign),
            'lastmod' => optional($campaign->updated_at)->toAtomString(),
        ];
    }
    // Empty catalog pages look like soft 404s and should not be submitted for indexing.
    foreach (Category::has('products')->orderBy('updated_at', 'desc')->get() as $cat) {
        $urls[] = [
            'loc' => route('categories.show', $cat->canonical_slug),
            'lastmod' => optional($cat->updated_at)->toAtomString(),
        ];
    }
    foreach (Product::orderBy('updated_at', 'desc')->take(1000)->get() as $p) {
        $urls[] = [
            'loc' => route('products.show', $p),
            'lastmod' => optional($p->updated_at)->toAtomString(),
            'image' => $p->image ? image_src($p->image) : null,
            'image_title' => $p->name,
        ];
    }
    return response()->view('sitemap', compact('urls'))->header('Content-Type', 'application/xml');
});

Route::get('/{key}.txt', function (string $key) {
    $configuredKey = trim((string) config('services.indexnow.key', ''));

    abort_if($configuredKey === '' || ! hash_equals($configuredKey, $key), 404);

    return response($configuredKey, 200)
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->where('key', '[A-Za-z0-9\-]+');

// Public Pages at root: /{slug}
Route::get('/{page:slug}', [PublicPageController::class, 'show'])
    ->where('page', '^(?!(admin|account|cart|checkout|products|deals|brands|campaigns|login|register|password|email|orders|sitemap|mpesa|api|dashboard)$)[A-Za-z0-9\-]+$')
    ->name('pages.show');

// Legacy redirect from /p/{slug} -> /{slug}
Route::get('/p/{page:slug}', function (\App\Models\Page $page) {
    return redirect()->to(route('pages.show', $page), 301);
});

// Fallback 404
Route::fallback(function () {
    abort(404);
});
