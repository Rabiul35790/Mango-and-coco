<?php

namespace App\Http\Controllers;

use App\Actions\Catalog\GetActiveProducts;
use App\Actions\Checkout\CreateLemonCheckout;
use App\Actions\VideoOrders\StoreVideoOrder;
use App\Enums\ProductKind;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function home(GetActiveProducts $catalog): Response
    {
        return Inertia::render('Home', [
            'products' => $catalog->handle(),
            'meta' => ['title' => 'Mango&Coco — Budgie sticker packs & personalised gift videos'],
        ]);
    }

    // Legacy routes → keep working, render same category-driven pages
    public function stickers(GetActiveProducts $catalog): Response
    {
        return $this->category('stickers', $catalog);
    }

    public function customVideos(GetActiveProducts $catalog): Response
    {
        return $this->category('video', $catalog);
    }

    public function coloringBooks(GetActiveProducts $catalog): Response
    {
        return $this->category('coloring-books', $catalog);
    }

    /** Generic category listing: /shop/{category} */
    public function category(string $categorySlug, GetActiveProducts $catalog): Response
    {
        $category = Category::where('slug', $categorySlug)->where('is_active', true)->firstOrFail();

        return Inertia::render('Category', [
            'category' => [
                'slug' => $category->slug,
                'name' => $category->name,
                'tagline' => $category->tagline,
                'description' => $category->description,
                'detailsHeading' => $category->details_heading,
                'detailsBody' => $category->details_body,
                'flow' => $category->flow(),
                'hero' => \App\Actions\Site\GetSiteData::hero([
                    'video' => $category->hero_video,
                    'poster' => $category->hero_poster ?: $category->hero_image,
                    'heading' => $category->hero_heading,
                    'subheading' => $category->hero_subheading,
                    'ctaLabel' => $category->hero_cta_label,
                    'ctaUrl' => $category->hero_cta_url,
                ]),
            ],
            'products' => $catalog->handle(category: $category),
            'meta' => ['title' => ($category->meta_title ?: $category->name.' — Mango&Coco')],
        ]);
    }

    /** Product details: /shop/{category}/{product} — instructions, gallery, demo video, buy/wizard CTA */
    public function product(string $categorySlug, string $productSlug, GetActiveProducts $catalog): Response
    {
        $category = Category::where('slug', $categorySlug)->where('is_active', true)->firstOrFail();
        $product = Product::with('category')->where('slug', $productSlug)
            ->where('is_active', true)->where('category_id', $category->id)->firstOrFail();

        // Sibling formats for wizard step 1 (other variants in same video/egift-card category)
        $siblings = $catalog->handle(category: $category);

        // Reviews: approved only, newest first + live aggregate.
        $approved = \App\Models\Review::with('user:id,name')
            ->where('product_id', $product->id)->where('status', 'approved')
            ->latest()->limit(10)->get();
        $user = request()->user();
        $purchased = $user && \App\Models\Order::where(function ($q) use ($user, $product) {
            $q->where('user_id', $user->id)->orWhere('customer_email', $user->email);
        })->where('product_slug', $product->slug)->where('status', 'paid')->exists();

        return Inertia::render('ProductDetails', [
            'category' => ['slug' => $category->slug, 'name' => $category->name, 'flow' => $category->flow()],
            'product' => $catalog->serialize($product),
            'siblings' => $siblings,
            'reviews' => [
                'average' => round($approved->avg('rating') ?? 0, 1),
                'count' => \App\Models\Review::where('product_id', $product->id)->where('status', 'approved')->count(),
                'items' => $approved->map(fn ($r) => [
                    'name' => $r->user?->name ?? 'Verified buyer',
                    'rating' => $r->rating,
                    'title' => $r->title,
                    'body' => $r->body,
                    'date' => $r->created_at->toDateString(),
                ])->all(),
            ],
            'canReview' => (bool) $purchased,
            'myReview' => $user ? \App\Models\Review::where('product_id', $product->id)->where('user_id', $user->id)->first()?->only(['rating', 'title', 'body', 'status']) : null,
            'meta' => ['title' => $product->meta_title ?: $product->name.' — Mango&Coco'],
        ]);
    }

    /** Wizard entry: /order/{product} for video + egift-card */
    public function wizard(string $productSlug, GetActiveProducts $catalog): Response
    {
        $product = Product::with('category')->where('slug', $productSlug)->where('is_active', true)->firstOrFail();
        abort_unless($product->usesWizard(), 404);

        $siblings = $catalog->handle(category: $product->category);

        return Inertia::render('OrderWizard', [
            'product' => $catalog->serialize($product),
            'siblings' => $siblings,
            'meta' => ['title' => 'Personalise — '.$product->name],
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('About', ['meta' => ['title' => 'Our Story — Mango&Coco']]);
    }

    public function contact(): Response
    {
        return Inertia::render('Contact', ['meta' => ['title' => 'Contact — Mango&Coco']]);
    }

    public function checkoutSuccess(Request $request): Response
    {
        // After Lemon payment: ?order=ID exposes a secure download button if file is ready.
        $downloadUrl = null;
        $trackingId = null;
        if ($request->query('order')) {
            $order = Order::find($request->query('order'));
            $product = $order ? Product::where('slug', $order->product_slug)->first() : null;
            $downloadUrl = $product?->temporaryDownloadUrl();
            $trackingId = $order?->tracking_id;
        }
        if (! $trackingId && $request->query('tracking')) {
            $trackingId = $request->query('tracking');
        }

        return Inertia::render('CheckoutSuccess', [
            'meta' => ['title' => 'Thank you — Mango&Coco'],
            'downloadUrl' => $downloadUrl,
            'trackingId' => $trackingId,
        ]);
    }

    public function checkout(Request $request, CreateLemonCheckout $action)
    {
        $data = $request->validate([
            'productSlug' => 'required|string|max:120',
            'email' => 'nullable|email|max:200',
        ]);

        return response()->json($action->handle($data['productSlug'], $data['email'] ?? null));
    }

    public function storeVideoOrder(Request $request, StoreVideoOrder $action)
    {
        $data = $request->validate([
            'productSlug' => 'required|string|max:120',
            'formatLabel' => 'nullable|string|max:160',
            'templateLabel' => 'nullable|string|max:120',
            'customerName' => 'required|string|max:120',
            'customerEmail' => 'required|email|max:200',
            'recipientName' => 'nullable|string|max:120',
            'recipientEmail' => 'nullable|email|max:200',
            'deliverTo' => 'required|in:self,other',
            'occasion' => 'nullable|string|max:120',
            'message' => 'required|string|max:400',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($data['deliverTo'] === 'other') {
            $request->validate(['recipientEmail' => 'required|email|max:200']);
        }

        if ($request->user()) {
            $data['userId'] = $request->user()->id;
        }

        $videoOrder = $action->handle($data);

        // Chain straight into Lemon checkout with the buyer email prefilled.
        $checkout = app(CreateLemonCheckout::class)->handle($data['productSlug'], $data['customerEmail']);

        // Keep the pay link on the brief so /track can offer "Complete payment".
        if (($checkout['configured'] ?? false) && isset($checkout['url'])) {
            $videoOrder->update(['checkout_url' => $checkout['url']]);
        }

        return response()->json([
            'videoOrderId' => $videoOrder->id,
            'trackingId' => $videoOrder->tracking_id,
            'checkout' => $checkout,
        ]);
    }

    /** Secure download: verifies a paid Order for this product before signing R2 URL.
     *  Logged-in owners skip the email check; guests prove ownership via email. */
    public function download(Request $request, string $productSlug)
    {
        $data = $request->validate([
            'email' => 'nullable|email|max:200',
            'order' => 'nullable|integer',
        ]);

        $product = Product::where('slug', $productSlug)->where('is_active', true)->firstOrFail();
        abort_unless($product->hasDownload(), 404, 'No file attached yet.');

        $query = Order::where('product_slug', $productSlug)->where('status', 'paid');

        if ($request->user()) {
            $u = $request->user();
            $query->where(function ($q) use ($u) {
                $q->where('user_id', $u->id)->orWhere('customer_email', $u->email);
            });
        } else {
            abort_unless(filled($data['email'] ?? null), 422, 'Email is required.');
            $query->where('customer_email', $data['email']);
        }

        if (! empty($data['order'])) {
            $query->where('id', $data['order']);
        }

        abort_unless($query->exists(), 403, 'No paid order found.');

        $url = $product->temporaryDownloadUrl(15);
        abort_unless($url, 500, 'Download not available. R2 not configured.');

        return response()->json(['url' => $url]);
    }

    public function storeContact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:200',
            'subject' => 'nullable|string|max:160',
            'message' => 'required|string|max:2000',
        ]);

        ContactMessage::create($data);

        return back()->with('success', 'Message sent. We reply within 2 working days.');
    }
}
