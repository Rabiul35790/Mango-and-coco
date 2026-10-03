<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ProductSeeder::class);
    }

    public function test_guests_cannot_open_account(): void
    {
        $this->get('/account')->assertRedirect('/login');
        $this->post('/account/reviews', [])->assertRedirect('/login');
    }

    public function test_register_creates_account_and_links_history(): void
    {
        Order::create([
            'product_slug' => 'mango-everyday-pack', 'customer_email' => 'ana@example.com',
            'total_cents' => 499, 'currency' => 'USD', 'status' => 'paid',
        ]);

        $res = $this->post('/register', [
            'name' => 'Ana', 'email' => 'ana@example.com',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ]);
        $res->assertRedirect('/account');
        $this->assertAuthenticated();

        $user = User::where('email', 'ana@example.com')->first();
        $this->assertNotNull($user);
        // Guest order auto-linked.
        $this->assertSame($user->id, Order::first()->fresh()->user_id);

        $this->get('/account')->assertOk();
        $this->get('/account/orders')->assertOk();
        $this->get('/account/downloads')->assertOk();
    }

    public function test_login_rejects_bad_credentials(): void
    {
        User::create(['name' => 'Bo', 'email' => 'bo@example.com', 'password' => bcrypt('password123')]);
        $this->post('/login', ['email' => 'bo@example.com', 'password' => 'wrong'])
            ->assertInvalid(['email']);
        $this->post('/login', ['email' => 'bo@example.com', 'password' => 'password123'])
            ->assertRedirect('/account');
    }

    public function test_reviews_are_purchase_gated_and_moderated(): void
    {
        $user = User::create(['name' => 'Cy', 'email' => 'cy@example.com', 'password' => bcrypt('password123')]);

        // No purchase → 403.
        $this->actingAs($user)->post('/account/reviews', [
            'productSlug' => 'mango-everyday-pack', 'rating' => 5,
        ])->assertForbidden();

        // Paid purchase → creates pending review.
        Order::create([
            'user_id' => $user->id, 'product_slug' => 'mango-everyday-pack',
            'customer_email' => 'cy@example.com', 'total_cents' => 499,
            'currency' => 'USD', 'status' => 'paid',
        ]);
        $this->actingAs($user)->post('/account/reviews', [
            'productSlug' => 'mango-everyday-pack', 'rating' => 5, 'title' => 'Cute', 'body' => 'Love',
        ])->assertRedirect();

        $review = Review::first();
        $this->assertSame('pending', $review->status);

        // Same user re-submits → updates, still one row.
        $this->actingAs($user)->post('/account/reviews', [
            'productSlug' => 'mango-everyday-pack', 'rating' => 4,
        ])->assertRedirect();
        $this->assertSame(1, Review::count());
        $this->assertSame(4, Review::first()->fresh()->rating);

        // Approved review is publicly visible on the details page.
        $review->update(['status' => 'approved']);
        $res = $this->get('/shop/stickers/mango-everyday-pack');
        $props = $res->viewData('page')['props'] ?? [];
        $this->assertSame(1, $props['reviews']['count']);
        $this->assertSame(4.0, (float) $props['reviews']['average']);
        $this->assertNotNull($props['myReview']);
    }

    public function test_logout_ends_session(): void
    {
        $user = User::create(['name' => 'De', 'email' => 'de@example.com', 'password' => bcrypt('password123')]);
        $this->actingAs($user)->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }
}
