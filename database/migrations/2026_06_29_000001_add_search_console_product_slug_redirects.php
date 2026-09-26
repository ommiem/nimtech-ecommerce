<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSlugRedirect;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $productRedirects = [
            'vitron-htc5568us-55-frameless-4k-uhd-android-tv' => 'vitron-htc5568us-55-frameless-4k-uhd-smart-android-tv',
            'oppo-a6-4g' => 'oppo-a6-pro-4G',
        ];

        foreach ($productRedirects as $oldSlug => $newSlug) {
            $product = Product::findByPublicSlug($newSlug);

            if ($product) {
                ProductSlugRedirect::rememberProductRedirect($oldSlug, $product);
            }
        }

        $phoneCategory = Category::findByPublicSlug('android')
            ?? Category::findByPublicSlug('phones');

        if ($phoneCategory) {
            ProductSlugRedirect::rememberCategoryFallback('samsung-galaxy-s25-fe-5g-512gb', $phoneCategory);
        }
    }

    public function down(): void
    {
        ProductSlugRedirect::query()
            ->whereIn('old_slug', [
                'vitron-htc5568us-55-frameless-4k-uhd-android-tv',
                'oppo-a6-4g',
                'samsung-galaxy-s25-fe-5g-512gb',
            ])
            ->delete();
    }
};
