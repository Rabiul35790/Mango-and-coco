<?php

namespace App\Models;

use App\Enums\VideoOrderStatus;
use App\Support\TrackingId;
use Illuminate\Database\Eloquent\Model;

class VideoOrder extends Model
{
    protected $fillable = [
        'user_id', 'tracking_id', 'estimated_delivery_at',
        'product_id', 'product_slug', 'format_label',
        'customer_name', 'customer_email',
        'recipient_name', 'recipient_email', 'occasion', 'message', 'notes',
        'deliver_to', 'checkout_url', 'lemon_order_id', 'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (VideoOrder $vo) {
            $vo->tracking_id ??= TrackingId::unique();
            // Custom work defaults to a 48-hour promise unless admin overrides.
            $vo->estimated_delivery_at ??= now()->addHours(48);
        });
    }

    protected $casts = [
        'status' => VideoOrderStatus::class,
        'estimated_delivery_at' => 'datetime',
    ];
}
