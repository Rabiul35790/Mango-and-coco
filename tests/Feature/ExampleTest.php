<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Smoke test: the storefront boots with a seeded catalog.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ProductSeeder::class);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
