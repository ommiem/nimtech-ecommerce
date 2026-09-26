<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlugs;
use App\Support\IndexNow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;
    use GeneratesUniqueSlugs;

    protected $fillable = [
        'category_id', 'brand_id', 'name', 'slug', 'description', 'seo_content', 'price', 'image', 'stock',
    ];

    protected $casts = [
        'price' => 'float',
    ];

    public static function normalizeSlug(?string $value): string
    {
        return Str::slug(rawurldecode((string) $value));
    }

    public function getCanonicalSlugAttribute(): string
    {
        return static::normalizeSlug($this->slug ?: $this->name);
    }

    public static function findByPublicSlug(?string $slug, bool $withTrashed = false): ?self
    {
        $slug = trim((string) $slug);
        if ($slug === '') {
            return null;
        }

        $query = $withTrashed ? static::query()->withTrashed() : static::query();
        $exactMatch = (clone $query)->where('slug', $slug)->first();
        if ($exactMatch) {
            return $exactMatch;
        }

        $normalizedSlug = static::normalizeSlug($slug);
        if ($normalizedSlug === '') {
            return null;
        }

        $caseInsensitiveMatch = (clone $query)
            ->whereRaw('LOWER(slug) = ?', [strtolower($normalizedSlug)])
            ->first();

        if ($caseInsensitiveMatch) {
            return $caseInsensitiveMatch;
        }

        return $query
            ->get()
            ->first(fn (self $product) => $product->canonical_slug === $normalizedSlug);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function priceHistory(): HasMany
    {
        return $this->hasMany(ProductPriceHistory::class)->orderByDesc('recorded_at');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saved(function (Product $product) {
            ProductSlugRedirect::query()
                ->where('old_slug', $product->canonical_slug)
                ->delete();

            $urls = [route('products.show', ['productSlug' => $product->canonical_slug])];
            if ($product->wasChanged('slug')) {
                $oldSlug = static::normalizeSlug((string) $product->getOriginal('slug'));
                if ($oldSlug !== '' && $oldSlug !== $product->canonical_slug) {
                    $urls[] = route('products.show', ['productSlug' => $oldSlug]);
                }
            }

            IndexNow::submitUrls($urls);

            if ($product->wasRecentlyCreated || $product->wasChanged('price')) {
                $product->priceHistory()->create([
                    'price' => $product->price,
                    'recorded_at' => now(),
                ]);
            }
        });

        static::updated(function (Product $product) {
            if ($product->wasChanged('slug')) {
                ProductSlugRedirect::rememberProductRedirect($product->getOriginal('slug'), $product);
            }
        });

        static::deleted(function (Product $product) {
            $product->loadMissing('category');

            if ($product->trashed()) {
                ProductSlugRedirect::rememberCategoryFallback($product->slug, $product->category);
            }

            IndexNow::submitUrl(route('products.show', ['productSlug' => $product->canonical_slug]));
        });

        static::restored(function (Product $product) {
            IndexNow::submitUrl(route('products.show', ['productSlug' => $product->canonical_slug]));
        });
    }
}
