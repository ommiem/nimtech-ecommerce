<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlugs;
use App\Support\IndexNow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;
    use GeneratesUniqueSlugs;
    protected $fillable = [
        'name', 'slug', 'description', 'image_path', 'is_featured', 'sort_order', 'parent_id',
    ];

    public static function normalizeSlug(?string $value): string
    {
        return Str::slug((string) $value);
    }

    public function getCanonicalSlugAttribute(): string
    {
        return static::normalizeSlug($this->slug ?: $this->name);
    }

    public static function findByPublicSlug(?string $slug): ?self
    {
        $slug = trim((string) $slug);
        if ($slug === '') {
            return null;
        }

        $exactMatch = static::query()->where('slug', $slug)->first();
        if ($exactMatch) {
            return $exactMatch;
        }

        $normalizedSlug = static::normalizeSlug($slug);
        if ($normalizedSlug === '') {
            return null;
        }

        return static::query()
            ->get()
            ->first(fn (self $category) => $category->canonical_slug === $normalizedSlug);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Category $category) {
            $urls = [route('categories.show', ['category' => $category->canonical_slug])];

            if ($category->wasChanged('slug')) {
                $oldSlug = static::normalizeSlug((string) $category->getOriginal('slug'));
                if ($oldSlug !== '' && $oldSlug !== $category->canonical_slug) {
                    $urls[] = route('categories.show', ['category' => $oldSlug]);
                }
            }

            IndexNow::submitUrls($urls);
        });

        static::deleting(function (Category $category) {
            // On soft delete, also soft-delete products
            if (method_exists($category, 'isForceDeleting') && $category->isForceDeleting()) {
                return; // no cascade on force delete
            }
            $category->products()->get()->each(function (Product $p) {
                $p->delete();
            });
        });

        static::restoring(function (Category $category) {
            // Restore soft-deleted products in this category
            $category->products()->onlyTrashed()->restore();
        });

        static::deleted(function (Category $category) {
            IndexNow::submitUrl(route('categories.show', ['category' => $category->canonical_slug]));
        });

        static::restored(function (Category $category) {
            IndexNow::submitUrl(route('categories.show', ['category' => $category->canonical_slug]));
        });
    }
}
