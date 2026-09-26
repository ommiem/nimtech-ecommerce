<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Fallback404Test extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_nested_path_returns_404_instead_of_redirecting_home(): void
    {
        $this->get('/legacy/path/that-does-not-exist')
            ->assertNotFound()
            ->assertSee('Page not found');
    }
}
