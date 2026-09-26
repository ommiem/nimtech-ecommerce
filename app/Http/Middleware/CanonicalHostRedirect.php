<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanonicalHostRedirect
{
    public function handle(Request $request, Closure $next)
    {
        $host = strtolower((string) $request->getHost());
        if ($host === '' || $this->shouldSkipCanonicalRedirect($host)) {
            return $next($request);
        }

        $configuredCanonicalHost = strtolower((string) (config('app.canonical_host') ?: env('CANONICAL_HOST', '')));
        $canonicalHost = $this->normalizeCanonicalHost($configuredCanonicalHost !== '' ? $configuredCanonicalHost : $host);
        if ($canonicalHost === '') {
            return $next($request);
        }

        $forwardedProto = strtolower((string) ($request->headers->get('x-forwarded-proto') ?? ''));
        $isHttps = $request->isSecure() || $forwardedProto === 'https';
        $requestUri = $request->getRequestUri() ?: '/';
        $canonicalUri = $this->normalizeCanonicalUri($requestUri);

        if (! $isHttps || $host !== $canonicalHost || $requestUri !== $canonicalUri) {
            $target = 'https://'.$canonicalHost.$canonicalUri;
            return redirect()->to($target, 301);
        }

        return $next($request);
    }

    protected function shouldSkipCanonicalRedirect(string $host): bool
    {
        return $host === 'localhost'
            || $host === '127.0.0.1'
            || str_ends_with($host, '.test')
            || str_ends_with($host, '.local');
    }

    protected function normalizeCanonicalHost(string $host): string
    {
        return preg_replace('/^www\./i', '', trim(strtolower($host))) ?: '';
    }

    protected function normalizeCanonicalUri(string $uri): string
    {
        $normalizedUri = preg_replace('#^/public(?:/|$)#i', '/', $uri) ?? $uri;
        if ($normalizedUri === '') {
            return '/';
        }

        return str_starts_with($normalizedUri, '/') ? $normalizedUri : '/'.$normalizedUri;
    }
}
