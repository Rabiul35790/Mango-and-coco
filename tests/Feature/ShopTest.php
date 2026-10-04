<?php

namespace Tests\Feature;

use App\Mail\BriefDeliveredMail;
use App\Mail\BriefReceivedMail;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ProductSeeder::class);
    }

    private function pageProps($response): array
    {
        return $response->viewData('page')['props'] ?? [];
    }

    public function test_home_renders_with_products(): void
    {
        $res = $this->get('/');
        $res->assertOk();
        $this->assertNotEmpty($this->pageProps($res)['products'] ?? []);
    }

    public function test_category_pages_render_and_unknown_404s(): void
    {
        foreach (['stickers', 'wallpaper', 'video', 'egift-card', 'coloring-books'] as $slug) {
            $this->get("/shop/{$slug}")->assertOk();
        }
        $this->get('/shop/nope')->assertNotFound();
    }

    public function test_product_details_renders_with_reviews_shape(): void
    {
        $res = $this->get('/shop/stickers/mango-everyday-pack');
        $res->assertOk();
        $props = $this->pageProps($res);
        $this->assertSame('mango-everyday-pack', $props['product']['slug']);
        $this->assertArrayHasKey('reviews', $props);
        $this->assertFalse($props['canReview']);
        $this->get('/shop/stickers/missing')->assertNotFound();
    }

    public function test_wizard_only_for_custom_products(): void
    {
        $this->get('/order/personalised-video')->assertOk();
        $this->get('/order/mango-everyday-pack')->assertNotFound();
    }

    public function test_checkout_reports_unconfigured_without_lemon_keys(): void
    {
        $res = $this->postJson('/checkout', ['productSlug' => 'mango-everyday-pack']);
        $res->assertOk()->assertJson(['configured' => false]);
    }

    public function test_video_order_validates_and_creates_with_tracking(): void
    {
        $bad = $this->postJson('/video-orders', ['productSlug' => 'personalised-video']);
        $bad->assertStatus(422);

        $ok = $this->postJson('/video-orders', [
            'productSlug' => 'personalised-video',
            'formatLabel' => 'Personalised Video',
            'customerName' => 'Ava',
            'customerEmail' => 'ava@example.com',
            'deliverTo' => 'self',
            'message' => 'Happy birthday!',
        ]);
        $ok->assertOk()->assertJsonStructure(['videoOrderId', 'trackingId', 'checkout']);

        $row = \App\Models\VideoOrder::where('customer_email', 'ava@example.com')->first();
        $this->assertNotNull($row);
        $this->assertMatchesRegularExpression('/^MC-[A-Z2-9]{8}$/', $row->tracking_id);
        $this->assertNotNull($row->estimated_delivery_at);
    }

    public function test_track_page_lookup_behaviour(): void
    {
        // Empty state.
        $this->get('/track')->assertOk();

        // Unknown code.
        $res = $this->get('/track?code=MC-XXXXXXXX');
        $res->assertOk();
        $this->assertNull($this->pageProps($res)['result']);
        $this->assertNotNull($this->pageProps($res)['lookupError']);

        // Paid shop order by tracking id (no email needed).
        $order = Order::create([
            'product_slug' => 'mango-everyday-pack', 'customer_email' => 'buyer@x.com',
            'total_cents' => 499, 'currency' => 'USD', 'status' => 'paid',
        ]);
        $res = $this->get('/track?code='.$order->tracking_id);
        $res->assertOk();
        $props = $this->pageProps($res);
        $this->assertSame($order->tracking_id, $props['result']['trackingId']);
        $this->assertNotEmpty($props['result']['steps']);

        // Lemon receipt number requires the purchase email.
        $order->update(['lemon_order_id' => '98765']);
        $this->get('/track?code=98765')->assertOk();
        $anon = $this->pageProps($this->get('/track?code=98765'))['result'];
        $this->assertNull($anon);
        $authed = $this->pageProps($this->get('/track?code=98765&email=buyer@x.com'))['result'];
        $this->assertSame($order->tracking_id, $authed['trackingId']);
    }

    public function test_lemon_webhook_route_uses_package_controller(): void
    {
        // Regression: our own stub once shadowed this URI and silently
        // dropped all payment webhooks (paid orders were never recorded).
        $route = collect(\Route::getRoutes())->first(
            fn ($r) => $r->uri() === 'lemon-squeezy/webhook' && in_array('POST', $r->methods())
        );
        $this->assertNotNull($route);
        $this->assertStringContainsString('WebhookController', $route->getActionName());
    }

    public function test_brief_lifecycle_sends_both_emails(): void
    {
        Mail::fake();

        $this->postJson('/video-orders', [
            'productSlug' => 'personalised-video',
            'formatLabel' => 'Personalised Video',
            'customerName' => 'Ava',
            'customerEmail' => 'ava@example.com',
            'deliverTo' => 'self',
            'message' => 'Happy birthday!',
        ])->assertOk();

        // Rendering runs even under fake — catches reserved-var collisions.
        Mail::assertSent(BriefReceivedMail::class);

        \App\Models\VideoOrder::first()->update(['status' => 'delivered']);
        Mail::assertSent(BriefDeliveredMail::class);
    }

    public function test_download_is_gated_by_paid_order(): void
    {
        $product = Product::where('slug', 'mango-everyday-pack')->first();
        $product->update(['download_path' => 'deliverables/test.zip', 'download_disk' => 'public']);

        // Guest without email → 422.
        $this->postJson('/download/mango-everyday-pack', [])->assertStatus(422);
        // Guest with email but no order → 403.
        $this->postJson('/download/mango-everyday-pack', ['email' => 'nobody@x.com'])->assertForbidden();
        // Paid buyer → signed URL.
        Order::create([
            'product_slug' => 'mango-everyday-pack', 'customer_email' => 'buyer@x.com',
            'total_cents' => 499, 'currency' => 'USD', 'status' => 'paid',
        ]);
        $this->postJson('/download/mango-everyday-pack', ['email' => 'buyer@x.com'])
            ->assertOk()->assertJsonStructure(['url']);
    }
}
