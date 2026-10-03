<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Support\TrackingId;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'tracking_id', 'estimated_delivery_at',
        'lemon_order_id', 'product_slug', 'variant_id',
        'customer_email', 'customer_name', 'total_cents', 'currency',
        'status', 'payload',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->tracking_id ??= TrackingId::unique();
        });
    }

    protected $casts = [
        'status' => OrderStatus::class,
        'payload' => 'array',
        'total_cents' => 'integer',
        'estimated_delivery_at' => 'datetime',
    ];
}
