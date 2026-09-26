<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class IndexNow
{
    public static function isEnabled(): bool
    {
        return (bool) config('services.indexnow.enabled')
            && static::key() !== ''
            && static::host() !== '';
    }

    public static function key(): string
    {
        return trim((string) config('services.indexnow.key', ''));
    }

    public static function endpoint(): string
    {
        return trim((string) config('services.indexnow.endpoint', 'https://api.indexnow.org/indexnow'));
    }

    public static function keyLocation(): string
    {
        $configured = trim((string) config('services.indexnow.key_location', ''));
        if ($configured !== '') {
            return $configured;
        }

        $key = static::key();
        $host = static::host();
        if ($key === '' || $host === '') {
            return '';
        }

        return 'https://'.$host.'/'.$key.'.txt';
    }

    public static function submitUrl(string $url): bool
    {
        return static::submitUrls([$url]);
    }

    public static function submitUrls(array $urls): bool
    {
        if (! static::isEnabled()) {
            return false;
        }

        $host = static::host();
        $urlList = collect($urls)
            ->map(fn ($url) => trim((string) $url))
            ->filter()
            ->unique()
            ->filter(function (string $url) use ($host) {
                return strtolower((string) parse_url($url, PHP_URL_HOST)) === $host;
            })
            ->values()
            ->all();

        if ($urlList === []) {
            return false;
        }

        try {
            $response = Http::asJson()
                ->timeout((int) config('services.indexnow.timeout', 5))
                ->post(static::endpoint(), [
                    'host' => $host,
                    'key' => static::key(),
                    'keyLocation' => static::keyLocation(),
                    'urlList' => $urlList,
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('IndexNow submission failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
                'urls' => $urlList,
            ]);
        } catch (Throwable $e) {
            report($e);
        }

        return false;
    }

    private static function host(): string
    {
        $configuredUrl = trim((string) config('app.url'));
        if ($configuredUrl === '') {
            return '';
        }

        $host = parse_url($configuredUrl, PHP_URL_HOST);
        if (!$host) {
            $host = parse_url('https://'.$configuredUrl, PHP_URL_HOST) ?: $configuredUrl;
        }

        return strtolower(trim((string) $host, '/'));
    }
}
