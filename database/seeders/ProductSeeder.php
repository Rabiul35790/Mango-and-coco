<?php

namespace Database\Seeders;

use App\Actions\Catalog\GetActiveProducts;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $cat = fn (string $slug) => Category::where('slug', $slug)->first()?->id;

        // Drop pre-category legacy rows (uncategorized, no downloads wired).
        Product::whereNull('category_id')->delete();

        $installChips = [
            'platforms_a_label' => 'Installs as a sticker pack',
            'platforms_a' => ['WhatsApp', 'Telegram'],
            'platforms_b_label' => 'Or send from your photos',
            'platforms_b' => ['iMessage', 'Instagram', 'Messenger'],
        ];

        $rows = [
            [
                'slug' => 'mango-everyday-pack',
                'category' => 'stickers',
                'name' => 'Mango Everyday Pack',
                'tagline' => '24 cut-out stickers · ZIP',
                'description' => 'Mango on his own: hellos, thank-yous and happy little dances for everyday chats.',
                'kind' => 'sticker_pack',
                'price_cents' => 499,
                'image_key' => 'sticker-pack-1',
                'delivery_type' => 'instant',
                'instructions' => "Unzip, then import into WhatsApp (stickers > +) or Telegram (@Stickers bot). 300 DPI printable.",
                'usage_steps' => ['Download the ZIP after payment', 'Unzip: 24 PNGs + sheet', 'Import to WhatsApp / Telegram', 'Or print at home'],
                'whats_included' => '24 transparent PNGs + 1 ready sheet + free future additions by email.',
                'rating' => 5.0,
                'reviews_count' => 16,
                'sort_order' => 1,
            ] + $installChips,
            [
                'slug' => 'coco-sweet-pack',
                'category' => 'stickers',
                'name' => 'Coco Sweet Pack',
                'tagline' => '24 cut-out stickers · ZIP',
                'description' => 'Coco on hers: gentle, cosy stickers for planners, journals and kind messages.',
                'kind' => 'sticker_pack',
                'price_cents' => 499,
                'image_key' => 'sticker-pack-2',
                'delivery_type' => 'instant',
                'instructions' => 'Same import flow as all sticker packs. Transparent backgrounds, 300 DPI.',
                'usage_steps' => ['Download the ZIP', 'Import to chat apps', 'Use in planners & journals'],
                'whats_included' => '24 transparent PNGs + 1 sheet.',
                'rating' => 5.0,
                'reviews_count' => 11,
                'sort_order' => 2,
            ] + $installChips,
            [
                'slug' => 'sunny-phone-wallpaper',
                'category' => 'wallpaper',
                'name' => 'Sunny Budgie Wallpaper',
                'tagline' => '3 sizes · ZIP',
                'description' => 'Mango & Coco blossom-branch wallpaper for iPhone, Android and desktop.',
                'kind' => 'sticker_pack',
                'price_cents' => 390,
                'image_key' => 'hero',
                'delivery_type' => 'instant',
                'instructions' => 'Download ZIP, pick your size, set via Settings > Wallpaper.',
                'usage_steps' => ['Download the ZIP', 'Pick iPhone / Android / desktop', 'Set as wallpaper'],
                'whats_included' => '3 JPGs (1170×2532, 1080×2400, 1920×1080).',
                'rating' => 4.9,
                'reviews_count' => 8,
                'platforms_a_label' => 'Made for',
                'platforms_a' => ['iPhone', 'Android', 'Desktop'],
                'platforms_b_label' => null,
                'platforms_b' => null,
                'sort_order' => 5,
            ],
            [
                'slug' => 'personalised-video',
                'category' => 'video',
                'name' => 'Personalised Video',
                'tagline' => 'Your message in the scene · 48h',
                'description' => 'A short custom budgie video with your message hand-lettered into the artwork.',
                'kind' => 'video',
                'price_cents' => 1900,
                'image_key' => 'hero',
                'delivery_type' => 'custom',
                'demo_video_poster' => '/images/hero-budgies.jpg',
                'whats_included' => 'HD video (mp4) with your message in the scene, delivered by email within 48 hours. One revision round included.',
                'usage_steps' => ['Personalise in 5 quick steps', 'Pay securely', 'Receive your video by email within 48 hours'],
                'sort_order' => 10,
            ],
            [
                'slug' => 'animated-ecard',
                'category' => 'egift-card',
                'name' => 'Budgie eGift Card',
                'tagline' => 'Personalised still card · 48h',
                'description' => 'A personalised still eGift card with your words hand-lettered into the scene. Perfect for birthdays & anniversaries.',
                'kind' => 'video',
                'price_cents' => 1200,
                'image_key' => 'ecard',
                'delivery_type' => 'custom',
                'whats_included' => 'High-resolution still card (PNG + JPG) with your message in the artwork, delivered by email within 48 hours. One revision round included.',
                'usage_steps' => ['Personalise in 5 quick steps', 'Pay securely', 'Receive your card by email within 48 hours'],
                'sort_order' => 11,
            ],
            [
                'slug' => 'budgie-garden-coloring-book',
                'category' => 'coloring-books',
                'name' => 'Budgie Garden Coloring Book',
                'tagline' => '24 printable pages · PDF ZIP',
                'description' => 'Hand-drawn garden pages to print and color at home. Instant download.',
                'kind' => 'coloring_book',
                'price_cents' => 690,
                'image_key' => 'sticker-pack-1',
                'delivery_type' => 'instant',
                'instructions' => 'Download ZIP, print at 100% on A4/Letter, color with anything.',
                'usage_steps' => ['Download the ZIP', 'Print pages', 'Color & share'],
                'whats_included' => '24-page PDF + 24 PNGs.',
                'rating' => 4.8,
                'reviews_count' => 6,
                'platforms_a_label' => 'Color with anything',
                'platforms_a' => ['Crayons', 'Markers', 'Colored pencils'],
                'platforms_b_label' => null,
                'platforms_b' => null,
                'sort_order' => 20,
            ],
        ];

        foreach ($rows as $row) {
            $category = $row['category'];
            unset($row['category']);
            Product::updateOrCreate(
                ['slug' => $row['slug']],
                $row + [
                    'category_id' => $cat($category),
                    'currency' => 'USD',
                    'is_active' => true,
                    'meta_title' => $row['name'].' — Mango&Coco',
                ]
            );
        }

        GetActiveProducts::flush();
    }
}
