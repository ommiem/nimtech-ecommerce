<?php

namespace Tests\Feature;

use App\Http\Middleware\CanonicalHostRedirect;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PublicCanonicalRedirectTest extends TestCase
{
    public function test_public_prefix_redirects_to_clean_https_apex_url(): void
    {
        config()->set('app.canonical_host', 'nimtech.co.ke');
        $middleware = new CanonicalHostRedirect();
        $request = Request::create('http://www.nimtech.co.ke/public/login', 'GET');

        $response = $middleware->handle($request, fn () => new Response('ok'));

        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('https://nimtech.co.ke/login', $response->headers->get('Location'));
    }
}
