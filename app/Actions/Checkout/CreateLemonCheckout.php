<?php

namespace App\Actions\Checkout;

use App\Models\Product;
use Illuminate\Support\Facades\Http;

/**
 * Creates a Lemon Squeezy hosted checkout.
 * Returns ['configured' => false] when credentials/variant missing
 * so the UI shows "opening soon" instead of breaking (same UX as reference).
 */
class CreateLemonCheckout
{
    /** @return array{configured: bool, url?: string, reason?: string} */
    public function handle(string $productSlug, ?string $email = null): array
    {
        $product = Product::where('slug', $productSlug)->where('is_active', true)->first();

        if (! $product || ! $product->lemon_variant_id) {
            return ['configured' => false, 'reason' => 'Checkout is not connected yet.'];
        }

        $apiKey = config('lemonsqueezy.api_key') ?? env('LEMON_SQUEEZY_API_KEY');
        $storeId = config('lemonsqueezy.store') ?? env('LEMON_SQUEEZY_STORE_ID');

        if (! $apiKey || ! $storeId) {
            return ['configured' => false, 'reason' => 'Checkout is not connected yet.'];
        }

        try {
            $response = Http::withToken($apiKey)
                ->accept('application/vnd.api+json')
                ->post('https://api.lemonsqueezy.com/v1/checkouts', [
                    'data' => [
                        'type' => 'checkouts',
                        'attributes' => [
                            'checkout_data' => [
                                'email' => $email,
                                'custom' => ['product_slug' => $product->slug],
                            ],
                        ],
                        'relationships' => [
                            'store' => ['data' => ['type' => 'stores', 'id' => (string) $storeId]],
                            'variant' => ['data' => ['type' => 'variants', 'id' => (string) $product->lemon_variant_id]],
                        ],
                    ],
                ]);

            $url = $response->json('data.attributes.url');

            return $url
                ? ['configured' => true, 'url' => $url]
                : ['configured' => false, 'reason' => 'Checkout could not be started.'];
        } catch (\Throwable $e) {
            report($e);

            return ['configured' => false, 'reason' => 'Checkout could not be started.'];
        }
    }
}
