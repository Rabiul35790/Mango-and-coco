<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\VideoOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TrackController extends Controller
{
    /**
     * Public order tracking.
     * - Tracking ID (MC-XXXXXXXX): works alone, it's unguessable.
     * - Lemon receipt number: guessable, so the purchase email is required.
     */
    public function show(Request $request): Response
    {
        $code = trim((string) $request->query('code', ''));
        $email = trim((string) $request->query('email', ''));
        $result = null;
        $error = null;

        if ($code !== '') {
            $result = $this->findByTracking($code);

            if (! $result && $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result = $this->findByReceipt($code, $email);
            }

            if (! $result) {
                $error = 'No order found — check the code and email, then try again.';
            }
        }

        return Inertia::render('Track', [
            'meta' => ['title' => 'Track your order — Mango&Coco'],
            'code' => $code,
            'email' => $email,
            'result' => $result,
            'lookupError' => $error,
        ]);
    }

    private function findByTracking(string $code): ?array
    {
        if ($order = Order::where('tracking_id', $code)->first()) {
            return $this->shopRow($order);
        }
        if ($brief = VideoOrder::where('tracking_id', $code)->first()) {
            return $this->briefRow($brief);
        }

        return null;
    }

    private function findByReceipt(string $code, string $email): ?array
    {
        $order = Order::where('lemon_order_id', $code)
            ->where('customer_email', $email)->first();

        return $order ? $this->shopRow($order) : null;
    }

    private function shopRow(Order $o): array
    {
        $product = Product::where('slug', $o->product_slug)->first();
        $status = $o->status instanceof \BackedEnum ? $o->status->value : $o->status;

        return [
            'type' => 'order',
            'trackingId' => $o->tracking_id,
            'product' => $product?->name ?? $o->product_slug,
            'date' => $o->created_at->toDateString(),
            'status' => $status,
            'steps' => $this->shopSteps($status),
            'eta' => $o->estimated_delivery_at?->format('M j, Y'),
            'etaPast' => $o->estimated_delivery_at?->isPast(),
            'downloadUrl' => $status === 'paid' ? $product?->temporaryDownloadUrl(30) : null,
            'message' => null,
        ];
    }

    private function briefRow(VideoOrder $v): array
    {
        $status = $v->status instanceof \BackedEnum ? $v->status->value : $v->status;

        return [
            'type' => 'brief',
            'trackingId' => $v->tracking_id,
            'product' => $v->format_label ?: $v->product_slug,
            'date' => $v->created_at->toDateString(),
            'status' => $status,
            'steps' => $this->briefSteps($status),
            'eta' => $v->estimated_delivery_at?->format('M j, Y'),
            'etaPast' => $v->estimated_delivery_at?->isPast(),
            'downloadUrl' => null,
            'message' => $v->message,
            'payUrl' => $status === 'new' ? $v->checkout_url : null,
        ];
    }

    /** Pending → Confirmed → Processing → Delivered (+ Cancelled). */
    private function briefSteps(string $status): array
    {
        if ($status === 'cancelled') {
            return [['label' => 'Cancelled', 'done' => true, 'current' => true]];
        }
        $order = ['new' => 0, 'confirmed' => 1, 'in_production' => 2, 'delivered' => 3];
        $labels = ['new' => 'Pending', 'confirmed' => 'Confirmed', 'in_production' => 'Processing', 'delivered' => 'Delivered'];
        $pos = $order[$status] ?? 0;

        return array_map(
            fn ($key, $i) => ['label' => $labels[$key], 'done' => $i <= $pos, 'current' => $i === $pos],
            array_keys($labels), array_keys(array_keys($labels))
        );
    }

    private function shopSteps(string $status): array
    {
        if (in_array($status, ['failed', 'refunded'], true)) {
            return [['label' => ucfirst($status), 'done' => true, 'current' => true]];
        }
        $order = ['pending' => 0, 'paid' => 1];
        $labels = ['pending' => 'Order placed', 'paid' => 'Paid', 'done' => 'Completed'];
        $pos = $status === 'paid' ? 2 : 0;

        return array_map(
            fn ($label, $i) => ['label' => $label, 'done' => $i <= $pos, 'current' => $i === $pos],
            array_values($labels), [0, 1, 2]
        );
    }
}
