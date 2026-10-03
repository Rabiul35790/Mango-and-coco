<?php

namespace App\Listeners;

use App\Enums\OrderStatus;
use App\Enums\VideoOrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\VideoOrder;
use LemonSqueezy\Laravel\Events\OrderCreated;

/**
 * Mirrors a paid Lemon Squeezy order into our shop orders table so the
 * customer panel and /track page have something to show.
 * Linked to a user account by email when one exists.
 */
class RecordShopOrder
{
    public function handle(OrderCreated $event): void
    {
        try {
            $attrs = $event->payload['data']['attributes'] ?? [];
            $email = $attrs['user_email'] ?? null;
            $variantId = $attrs['first_order_item']['variant_id'] ?? null;
            $lemonId = (string) ($event->payload['data']['id'] ?? '');

            if (! $email) {
                return;
            }

            $product = $variantId
                ? Product::where('lemon_variant_id', (string) $variantId)->first()
                : null;

            $userId = User::where('email', $email)->first()?->id;

            $data = [
                'user_id' => $userId,
                'product_slug' => $product?->slug ?? 'unknown',
                'variant_id' => $variantId ? (string) $variantId : null,
                'customer_email' => $email,
                'customer_name' => $attrs['user_name'] ?? null,
                'total_cents' => (int) round((float) ($attrs['total'] ?? 0)),
                'currency' => strtoupper($attrs['currency'] ?? 'USD'),
                'status' => OrderStatus::Paid,
                'payload' => $event->payload,
            ];

            // Match on the Lemon id when we have one — never overwrite an
            // unrelated row when the id is missing (repeat buyers).
            if ($lemonId !== '') {
                $order = Order::updateOrCreate(['lemon_order_id' => $lemonId], $data);
            } else {
                $order = Order::create($data + ['lemon_order_id' => null]);
            }

            // Payment confirms the matching custom brief automatically:
            // newest unpaid brief for this email + product.
            if ($product) {
                VideoOrder::where('customer_email', $email)
                    ->where('product_slug', $product->slug)
                    ->where('status', VideoOrderStatus::New)
                    ->latest()->limit(1)
                    ->update([
                        'status' => VideoOrderStatus::Confirmed,
                        'lemon_order_id' => $lemonId !== '' ? $lemonId : null,
                    ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
