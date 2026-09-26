<?php

namespace Tests\Unit;

use Tests\TestCase;

class CanonicalUrlHelperTest extends TestCase
{
    public function test_canonical_url_strips_public_prefix_and_www_host(): void
    {
        config()->set('app.canonical_host', 'nimtech.co.ke');

        $this->assertSame(
            'https://nimtech.co.ke/category/laptop-chargers',
            canonical_url('https://www.nimtech.co.ke/public/category/laptop-chargers')
        );
    }

    public function test_canonical_url_preserves_query_string_when_present(): void
    {
        config()->set('app.canonical_host', 'nimtech.co.ke');

        $this->assertSame(
            'https://nimtech.co.ke/products?q=laptop',
            canonical_url('/public/products?q=laptop')
        );
    }

    public function test_canonical_url_normalizes_full_host_only_url(): void
    {
        config()->set('app.canonical_host', 'nimtech.co.ke');

        $this->assertSame(
            'https://nimtech.co.ke/',
            canonical_url('http://www.nimtech.co.ke')
        );
    }
}
