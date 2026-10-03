<?php

namespace App\Actions\VideoOrders;

use App\Enums\VideoOrderStatus;
use App\Models\Product;
use App\Models\VideoOrder;

class StoreVideoOrder
{
    public function handle(array $data): VideoOrder
    {
        $product = Product::where('slug', $data['productSlug'])->first();
        $format = $data['formatLabel'] ?? $product?->name;
        if (! empty($data['templateLabel'])) {
            $format = ($format ? $format.' — ' : '').$data['templateLabel'];
        }

        return VideoOrder::create([
            'user_id' => $data['userId'] ?? null,
            'product_id' => $product?->id,
            'product_slug' => $data['productSlug'],
            'format_label' => $format,
            'customer_name' => $data['customerName'],
            'customer_email' => $data['customerEmail'],
            'recipient_name' => $data['recipientName'] ?? null,
            'recipient_email' => $data['recipientEmail'] ?? ($data['deliverTo'] === 'other' ? null : $data['customerEmail']),
            'occasion' => $data['occasion'] ?? null,
            'message' => $data['message'],
            'notes' => $data['notes'] ?? null,
            'deliver_to' => $data['deliverTo'] ?? 'self',
            'status' => VideoOrderStatus::New->value,
        ]);
    }
}
