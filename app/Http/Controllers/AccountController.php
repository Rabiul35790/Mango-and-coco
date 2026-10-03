<?php

namespace App\Http\Controllers;

use App\Actions\Catalog\GetActiveProducts;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\VideoOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function dashboard(): Response
    {
        $user = request()->user();

        $orders = Order::where('user_id', $user->id)->latest()->limit(5)->get();
        $videoOrders = VideoOrder::where('user_id', $user->id)->latest()->limit(5)->get();
        $downloads = $this->downloadItems($user);

        return Inertia::render('Account/Dashboard', [
            'meta' => ['title' => 'My account — Mango&Coco'],
            'stats' => [
                'orders' => Order::where('user_id', $user->id)->count(),
                'videoOrders' => VideoOrder::where('user_id', $user->id)->count(),
                'downloads' => count($downloads),
                'reviews' => Review::where('user_id', $user->id)->count(),
            ],
            'recentOrders' => $orders->map(fn ($o) => $this->orderRow($o))->all(),
            'recentVideoOrders' => $videoOrders->map(fn ($v) => [
                'id' => $v->id,
                'trackingId' => $v->tracking_id,
                'product' => $v->format_label ?: $v->product_slug,
                'status' => $v->status instanceof \BackedEnum ? $v->status->value : $v->status,
                'date' => $v->created_at->toDateString(),
            ])->all(),
        ]);
    }

    public function orders(): Response
    {
        $user = request()->user();
        $orders = Order::where('user_id', $user->id)->latest()->get();
        $videoOrders = VideoOrder::where('user_id', $user->id)->latest()->get();

        return Inertia::render('Account/Orders', [
            'meta' => ['title' => 'My orders — Mango&Coco'],
            'orders' => $orders->map(fn ($o) => $this->orderRow($o))->all(),
            'videoOrders' => $videoOrders->map(fn ($v) => [
                'id' => $v->id,
                'trackingId' => $v->tracking_id,
                'product' => $v->format_label ?: $v->product_slug,
                'message' => $v->message,
                'deliverTo' => $v->deliver_to,
                'status' => $v->status instanceof \BackedEnum ? $v->status->value : $v->status,
                'date' => $v->created_at->toDateString(),
            ])->all(),
        ]);
    }

    public function downloads(GetActiveProducts $catalog): Response
    {
        return Inertia::render('Account/Downloads', [
            'meta' => ['title' => 'My downloads — Mango&Coco'],
            'items' => $this->downloadItems(request()->user()),
        ]);
    }

    /** Paid orders whose product has a deliverable file → fresh signed URLs. */
    private function downloadItems($user): array
    {
        return Order::where('user_id', $user->id)->where('status', 'paid')
            ->latest()->get()
            ->map(function ($o) {
                $product = Product::where('slug', $o->product_slug)->first();

                return [
                    'orderId' => $o->id,
                    'product' => $product?->name ?? $o->product_slug,
                    'date' => $o->created_at->toDateString(),
                    'url' => $product?->temporaryDownloadUrl(30),
                ];
            })
            ->filter(fn ($i) => filled($i['url']))
            ->values()->all();
    }

    private function orderRow($o): array
    {
        $product = Product::where('slug', $o->product_slug)->first();

        return [
            'id' => $o->id,
            'trackingId' => $o->tracking_id,
            'product' => $product?->name ?? $o->product_slug,
            'total' => number_format($o->total_cents / 100, 2).' '.strtoupper($o->currency),
            'status' => $o->status instanceof \BackedEnum ? $o->status->value : $o->status,
            'date' => $o->created_at->toDateString(),
            'hasDownload' => (bool) $product?->hasDownload(),
        ];
    }

    public function storeReview(Request $request)
    {
        $data = $request->validate([
            'productSlug' => 'required|string|max:120',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:160',
            'body' => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $product = Product::where('slug', $data['productSlug'])->where('is_active', true)->firstOrFail();

        // Purchase-gated: must own a paid order for this product.
        abort_unless(
            Order::where('user_id', $user->id)->where('product_slug', $product->slug)->where('status', 'paid')->exists()
            || Order::where('customer_email', $user->email)->where('product_slug', $product->slug)->where('status', 'paid')->exists(),
            403, 'Only verified buyers can review.'
        );

        Review::updateOrCreate(
            ['product_id' => $product->id, 'user_id' => $user->id],
            ['rating' => $data['rating'], 'title' => $data['title'] ?? null, 'body' => $data['body'] ?? null, 'status' => 'pending']
        );

        (new GetActiveProducts)->flush();

        return back()->with('success', 'Thanks! Your review is awaiting moderation.');
    }
}
