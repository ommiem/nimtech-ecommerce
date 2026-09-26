<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

trait GeneratesUniqueSlugs
{
    public static function makeUniqueSlug(
        string $baseSlug,
        ?int $ignoreId = null,
        string $column = 'slug',
        int $maxLength = 255
    ): string {
        $baseSlug = trim($baseSlug);
        if ($baseSlug === '') {
            $baseSlug = Str::random(8);
        }

        $baseSlug = static::truncateSlug($baseSlug, $maxLength);
        $slug = $baseSlug;
        $counter = 2;

        while (static::slugExists($column, $slug, $ignoreId)) {
            $suffix = '-'.$counter++;
            $slug = static::truncateSlug($baseSlug, $maxLength - strlen($suffix)).$suffix;
        }

        return $slug;
    }

    protected static function slugExists(string $column, string $slug, ?int $ignoreId): bool
    {
        return static::slugQuery($column, $slug, $ignoreId)->exists();
    }

    protected static function slugQuery(string $column, string $slug, ?int $ignoreId): Builder
    {
        $query = static::query();
        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            $query = $query->withTrashed();
        }

        $query->where($column, $slug);
        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query;
    }

    protected static function truncateSlug(string $slug, int $maxLength): string
    {
        if ($maxLength <= 0) {
            return '';
        }

        return rtrim(Str::of($slug)->substr(0, $maxLength)->value(), '-');
    }
}

