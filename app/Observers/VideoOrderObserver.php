<?php

namespace App\Observers;

use App\Enums\VideoOrderStatus;
use App\Mail\BriefDeliveredMail;
use App\Mail\BriefReceivedMail;
use App\Models\Product;
use App\Models\VideoOrder;
use Illuminate\Support\Facades\Mail;

/**
 * Customer emails for custom orders. All sends are guarded so a mail
 * failure can never break brief creation or admin saves.
 */
class VideoOrderObserver
{
    public function created(VideoOrder $vo): void
    {
        try {
            $product = Product::where('slug', $vo->product_slug)->first();
            Mail::to($vo->customer_email)->send(new BriefReceivedMail(
                name: $vo->customer_name,
                product: $vo->format_label ?: $product?->name ?? 'your personalised piece',
                trackingId: $vo->tracking_id,
                briefMessage: $vo->message,
                trackUrl: url('/track?code='.$vo->tracking_id),
            ));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function updated(VideoOrder $vo): void
    {
        if (! $vo->wasChanged('status') || $vo->status !== VideoOrderStatus::Delivered) {
            return;
        }

        try {
            $product = Product::where('slug', $vo->product_slug)->first();
            $reviewUrl = $product
                ? url("/shop/{$product->category?->slug}/{$product->slug}#reviews")
                : url('/track?code='.$vo->tracking_id);
            Mail::to($vo->customer_email)->send(new BriefDeliveredMail(
                name: $vo->customer_name,
                product: $vo->format_label ?: $product?->name ?? 'your personalised piece',
                trackingId: $vo->tracking_id,
                deliveryUrl: $vo->delivery_url,
                reviewUrl: $reviewUrl,
            ));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
