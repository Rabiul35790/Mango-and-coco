<?php

namespace App\Actions\Catalog;

use App\Enums\ProductKind;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

/**
 * Single read path for the storefront catalog.
 * Cached 5 min + invalidated on Filament saves.
 */
class GetActiveProducts
{
    /** @return array<int, array<string, mixed>> */
    public function handle(?ProductKind $kind = null, ?Category $category = null): array
    {
        $key = 'catalog.products.'.($category?->slug ?? 'all').'.'.($kind?->value ?? 'all');

        return Cache::remember($key, 300, fn () => Product::query()
            ->with(['category:id,slug,name', 'templates'])
            ->active()
            ->ofKind($kind)
            ->ofCategory($category)
            ->ordered()
            ->get()
            ->map(fn (Product $p) => $this->serialize($p))
            ->all());
    }

    /** @return array<string, mixed> */
    public function serialize(Product $p): array
    {
        $p->loadMissing(['category:id,slug,name', 'templates']);

        $flat = function ($v) {
            if (! is_array($v)) {
                return [];
            }

            return array_values(array_filter(array_map(
                fn ($i) => is_array($i) ? ($i['url'] ?? $i['step'] ?? null) : $i,
                $v
            )));
        };

        // Admin can upload the demo video file or paste a URL — file wins.
        // Storage paths resolve to relative /storage URLs (host-independent).
        $demoVideo = $p->demo_video_file
            ? (str_starts_with($p->demo_video_file, 'http') || str_starts_with($p->demo_video_file, '/')
                ? $p->demo_video_file
                : '/storage/'.ltrim($p->demo_video_file, '/'))
            : $p->demo_video_url;

        $fileUrl = function (?string $v): ?string {
            if (! filled($v)) {
                return null;
            }
            if (str_starts_with($v, 'http') || str_starts_with($v, '/')) {
                return $v;
            }

            return '/storage/'.ltrim($v, '/');
        };

        return [
            'id' => $p->id,
            'slug' => $p->slug,
            'name' => $p->name,
            'tagline' => $p->tagline,
            'description' => $p->description,
            'kind' => $p->kind->value,
            'category' => $p->category ? ['slug' => $p->category->slug, 'name' => $p->category->name] : null,
            'categorySlug' => $p->category?->slug,
            'priceCents' => $p->price_cents,
            'currency' => $p->currency,
            'imageKey' => $p->image_key,
            'coverUrl' => $fileUrl($p->cover_image),
            'gallery' => array_values(array_filter(array_map($fileUrl, $flat($p->gallery)))),
            'rating' => $p->rating,
            'reviewsCount' => (int) ($p->reviews_count ?? 0),
            'platformsA' => ['label' => $p->platforms_a_label, 'items' => $flat($p->platforms_a)],
            'platformsB' => ['label' => $p->platforms_b_label, 'items' => $flat($p->platforms_b)],
            'templates' => $p->templates->map(function ($t) use ($p, $fileUrl) {
                $file = $fileUrl($t->file);
                $poster = $fileUrl($t->poster);
                $isVideo = ($p->category?->slug ?? '') === 'video';

                return [
                    'id' => $t->id,
                    'label' => $t->label,
                    'fileUrl' => $file,
                    'posterUrl' => $poster,
                    'isVideo' => $isVideo,
                    // Image templates thumb = the file itself; video thumbs =
                    // poster (frontend falls back to product cover when null).
                    'thumbUrl' => $poster ?? ($isVideo ? null : $file),
                ];
            })->all(),
            'demoVideoUrl' => $demoVideo,
            'demoVideoPoster' => $p->demo_video_poster,
            'instructions' => $p->instructions,
            'usageSteps' => $flat($p->usage_steps),
            'whatsIncluded' => $p->whats_included,
            'deliveryType' => $p->delivery_type,
            'usesWizard' => $p->usesWizard(),
            'hasDownload' => $p->hasDownload(),
            'lemonVariantId' => $p->lemon_variant_id,
            'isInstantDownload' => $p->kind->isInstantDownload(),
            'ctaLabel' => $p->kind->ctaLabel(),
            'detailsUrl' => $p->category ? "/shop/{$p->category->slug}/{$p->slug}" : "/shop/all/{$p->slug}",
        ];
    }

    public static function flush(): void
    {
        foreach (['all', 'sticker_pack', 'video', 'coloring_book', 'ecard'] as $k) {
            Cache::forget('catalog.products.all.'.$k);
        }
        foreach (['stickers', 'wallpaper', 'video', 'egift-card', 'coloring-books', 'all'] as $c) {
            foreach (['all', 'sticker_pack', 'video', 'coloring_book', 'ecard'] as $k) {
                Cache::forget("catalog.products.{$c}.{$k}");
            }
        }
        Cache::forget('site.settings');
        Cache::forget('site.categories');
    }
}
