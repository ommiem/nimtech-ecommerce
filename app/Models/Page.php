<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlugs;
use App\Support\IndexNow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Page extends Model
{
    use GeneratesUniqueSlugs;
    protected $fillable = [
        'title','slug','content','featured_image','published','category_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saved(function (Page $page) {
            $urls = [];
            $oldSlug = Str::slug((string) $page->getOriginal('slug'));

            if ($page->wasChanged('slug') && $oldSlug !== '' && $oldSlug !== $page->slug) {
                $urls[] = route('pages.show', ['page' => $oldSlug]);
            }

            if ($page->published) {
                $urls[] = route('pages.show', ['page' => $page->slug]);
            } elseif ($page->wasChanged('published') && (bool) $page->getOriginal('published')) {
                $urls[] = route('pages.show', ['page' => $page->slug]);
            }

            IndexNow::submitUrls($urls);
        });

        static::deleted(function (Page $page) {
            if ($page->published) {
                IndexNow::submitUrl(route('pages.show', ['page' => $page->slug]));
            }
        });
    }
}
