<?php

namespace App\Actions\Site;

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class GetSiteData
{
    public static function settings(): array
    {
        return Cache::remember('site.settings', 600, function () {
            $s = Setting::current();

            return [
                'siteName' => $s->site_name,
                'logoUrl' => $s->logoUrl(),
                'address' => $s->address,
                'contactEmail' => $s->contact_email,
                'supportEmail' => $s->support_email,
                'copyright' => $s->copyright(),
                'tracking' => [
                    'metaPixelId' => $s->meta_pixel_id,
                    'tiktokPixelId' => $s->tiktok_pixel_id,
                    'googleAnalyticsId' => $s->google_analytics_id,
                    'microsoftClarityId' => $s->microsoft_clarity_id,
                ],
                'extraHeadScripts' => $s->extra_head_scripts,
                'extraBodyScripts' => $s->extra_body_scripts,
                'hero' => self::hero([
                    'video' => $s->hero_video,
                    'poster' => $s->hero_poster,
                    'heading' => $s->hero_heading,
                    'subheading' => $s->hero_subheading,
                    'ctaLabel' => $s->hero_cta_label,
                    'ctaUrl' => $s->hero_cta_url,
                    'secondaryCtaLabel' => $s->hero_secondary_cta_label,
                    'secondaryCtaUrl' => $s->hero_secondary_cta_url,
                ]),
            ];
        });
    }

    /** Resolve hero video/poster storage paths to URLs (absolute URLs pass through). */
    public static function hero(array $h): array
    {
        $url = function (?string $v): ?string {
            if (! filled($v)) {
                return null;
            }
            if (str_starts_with($v, 'http') || str_starts_with($v, '/')) {
                return $v;
            }

            // Relative URL on purpose: works on artisan serve, Herd, and
            // production alike, regardless of what APP_URL is set to.
            return '/storage/'.ltrim($v, '/');
        };

        return [
            'videoUrl' => $url($h['video'] ?? null),
            'posterUrl' => $url($h['poster'] ?? null),
            'heading' => $h['heading'] ?? null,
            'subheading' => $h['subheading'] ?? null,
            'ctaLabel' => $h['ctaLabel'] ?? null,
            'ctaUrl' => $h['ctaUrl'] ?? null,
            'secondaryCtaLabel' => $h['secondaryCtaLabel'] ?? null,
            'secondaryCtaUrl' => $h['secondaryCtaUrl'] ?? null,
        ];
    }

    public static function categories(): array
    {
        return Cache::remember('site.categories', 600, fn () => Category::where('is_active', true)
            ->orderBy('sort_order')->orderBy('name')
            ->get(['slug', 'name', 'tagline', 'description'])
            ->map(fn ($c) => [
                'slug' => $c->slug,
                'name' => $c->name,
                'tagline' => $c->tagline,
                'description' => $c->description,
                'url' => "/shop/{$c->slug}",
            ])->all());
    }
}
