<?php

namespace Tests\Feature;

use App\Support\IndexNow;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IndexNowTest extends TestCase
{
    public function test_indexnow_key_route_returns_configured_key(): void
    {
        config()->set('services.indexnow.key', 'abc-123-indexnow');

        $this->get('/abc-123-indexnow.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('abc-123-indexnow');
    }

    public function test_indexnow_submission_posts_same_host_urls_only(): void
    {
        config()->set('app.url', 'https://nimtech.co.ke');
        config()->set('services.indexnow.enabled', true);
        config()->set('services.indexnow.key', 'abc-123-indexnow');
        config()->set('services.indexnow.endpoint', 'https://api.indexnow.org/indexnow');

        Http::fake([
            'https://api.indexnow.org/indexnow' => Http::response([], 200),
        ]);

        $this->assertTrue(IndexNow::submitUrls([
            'https://nimtech.co.ke/products',
            'https://nimtech.co.ke/category/laptops',
            'https://example.com/not-allowed',
        ]));

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.indexnow.org/indexnow'
                && $request['host'] === 'nimtech.co.ke'
                && $request['key'] === 'abc-123-indexnow'
                && $request['keyLocation'] === IndexNow::keyLocation()
                && $request['urlList'] === [
                    'https://nimtech.co.ke/products',
                    'https://nimtech.co.ke/category/laptops',
                ];
        });
    }
}
