<?php

use App\Models\Setting;

if (! function_exists('currency_format')) {
    function currency_format(float|int $amount): string
    {
        $s = Setting::getCached();
        $symbol = $s?->currency_symbol ?? '$';
        $position = $s?->currency_position ?? 'left';
        $formatted = number_format((float) $amount, 2);
        return $position === 'right' ? ($formatted . ' ' . $symbol) : ($symbol . $formatted);
    }
}

if (! function_exists('whatsapp_link')) {
    function whatsapp_link(string $text): string
    {
        $s = Setting::getCached();
        $raw = (string) ($s->contact_phone ?? '');
        $digits = preg_replace('/\D+/', '', $raw ?? '');
        if ($digits && str_starts_with($digits, '0')) {
            $digits = '254' . substr($digits, 1);
        }
        $encoded = rawurlencode($text);
        return $digits ? ("https://wa.me/{$digits}?text={$encoded}") : ("https://wa.me/?text={$encoded}");
    }
}

if (! function_exists('image_src')) {
    /**
     * Return a URL for an image in storage, preferring a .webp version if it exists.
     * Pass the path relative to storage/app/public (e.g., "uploads/pic.jpg").
     */
    function image_src(?string $relativePath): ?string
    {
        if (!$relativePath) return null;
        $public = storage_path('app/public/');
        $path = str_replace(['..', '\\'], ['', '/'], $relativePath);
        $webp = preg_replace('/\.[^.]+$/', '.webp', $path);
        if ($webp && is_file($public.$webp)) {
            return asset('storage/'.$webp);
        }
        return asset('storage/'.$path);
    }
}

if (! function_exists('canonical_url')) {
    function canonical_url(?string $path = null): string
    {
        $value = trim((string) ($path ?? request()->getRequestUri() ?? '/'));
        if ($value === '') {
            $value = '/';
        }

        $parsed = parse_url($value);
        if (
            $parsed !== false &&
            (
                array_key_exists('path', $parsed) ||
                array_key_exists('host', $parsed) ||
                array_key_exists('scheme', $parsed)
            )
        ) {
            $value = (string) ($parsed['path'] ?? '/');
            if ($value === '') {
                $value = '/';
            }
            if (! empty($parsed['query'])) {
                $value .= '?'.$parsed['query'];
            }
        }

        $value = '/'.ltrim($value, '/');
        if ($value === '/public') {
            $value = '/';
        } elseif (str_starts_with($value, '/public/')) {
            $value = substr($value, 7);
        }

        $host = strtolower((string) (config('app.canonical_host') ?: env('CANONICAL_HOST') ?: request()->getHost()));
        if ($host === '') {
            $host = 'localhost';
        }
        if (str_starts_with($host, 'www.')) {
            $host = substr($host, 4);
        }

        return 'https://'.$host.$value;
    }
}
