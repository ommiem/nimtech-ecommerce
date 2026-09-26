<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSlugRedirect extends Model
{
    protected $fillable = [
        'old_slug',
        'product_id',
        'category_id',
    ];

    public function setOldSlugAttribute(?string $value): void
    {
        $this->attributes['old_slug'] = Product::normalizeSlug($value);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public static function findByPublicSlug(?string $slug): ?self
    {
        $normalizedSlug = Product::normalizeSlug($slug);
        if ($normalizedSlug === '') {
            return null;
        }

        return static::query()
            ->with(['product.category', 'category'])
            ->where('old_slug', $normalizedSlug)
            ->first();
    }

    public static function rememberProductRedirect(?string $oldSlug, Product $product): void
    {
        $normalizedOldSlug = Product::normalizeSlug($oldSlug);
        if ($normalizedOldSlug === '' || $normalizedOldSlug === $product->canonical_slug) {
            return;
        }

        static::query()->updateOrCreate(
            ['old_slug' => $normalizedOldSlug],
            [
                'product_id' => $product->id,
                'category_id' => null,
            ]
        );
    }

    public static function rememberCategoryFallback(?string $oldSlug, ?Category $category): void
    {
        $normalizedOldSlug = Product::normalizeSlug($oldSlug);
        if ($normalizedOldSlug === '') {
            return;
        }

        static::query()->updateOrCreate(
            ['old_slug' => $normalizedOldSlug],
            [
                'product_id' => null,
                'category_id' => $category?->id,
            ]
        );
    }
}
