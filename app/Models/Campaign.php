<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlugs;
use App\Support\IndexNow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Campaign extends Model
{
    use GeneratesUniqueSlugs;

    protected $fillable = [
        'title',
        'slug',
        'hero_badge',
        'summary',
        'content',
        'featured_image',
        'meta_title',
        'meta_description',
        'cta_label',
        'cta_url',
        'whatsapp_message',
        'promotion_id',
        'compare_enabled',
        'published',
    ];

    protected $casts = [
        'compare_enabled' => 'bool',
        'published' => 'bool',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['quantity', 'sort_order'])
            ->withTimestamps()
            ->orderBy('campaign_product.sort_order')
            ->orderBy('campaign_product.id');
    }

    protected static function booted(): void
    {
        static::saved(function (Campaign $campaign) {
            $urls = [];
            $oldSlug = Str::slug((string) $campaign->getOriginal('slug'));

            if ($campaign->wasChanged('slug') && $oldSlug !== '' && $oldSlug !== $campaign->slug) {
                $urls[] = route('campaigns.show', ['campaign' => $oldSlug]);
            }

            if ($campaign->published) {
                $urls[] = route('campaigns.show', $campaign);
            } elseif ($campaign->wasChanged('published') && (bool) $campaign->getOriginal('published')) {
                $urls[] = route('campaigns.show', $campaign);
            }

            IndexNow::submitUrls($urls);
        });

        static::deleted(function (Campaign $campaign) {
            if ($campaign->published) {
                IndexNow::submitUrl(route('campaigns.show', ['campaign' => $campaign->slug]));
            }
        });
    }
}
