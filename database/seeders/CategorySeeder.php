<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'slug' => 'stickers',
                'name' => 'Sticker Packs',
                'tagline' => 'For WhatsApp, Telegram & planners',
                'description' => 'Digital budgie sticker packs. Buy once, download the zip, use everywhere.',
                'details_heading' => 'How to use your stickers',
                'details_body' => "1. Download the zip after payment.\n2. Unzip: 24 transparent PNGs + 1 sheet.\n3. WhatsApp: open a chat > emoji > stickers > + > import.\n4. Telegram: @Stickers bot > /newpack > upload PNGs (512px).\n5. Print at 300 DPI on sticker paper for personal use.",
                'sort_order' => 1,
            ],
            [
                'slug' => 'wallpaper',
                'name' => 'Mobile Wallpapers',
                'tagline' => 'Cozy phone backgrounds',
                'description' => 'Hand-drawn Mango & Coco wallpapers for your phone. Instant zip download.',
                'details_heading' => 'How to set your wallpaper',
                'details_body' => "1. Download the zip after payment.\n2. Pick your size (iPhone / Android / desktop included).\n3. Save to Photos, then Settings > Wallpaper > Choose photo.\n4. Printing? Files are 300 DPI for personal use.",
                'sort_order' => 2,
            ],
            [
                'slug' => 'video',
                'name' => 'Personalised Gift Videos',
                'tagline' => 'Your message in the scene',
                'description' => 'Short custom budgie videos starring Mango & Coco, with your message hand-lettered into the artwork. Delivered within 48 hours.',
                'sort_order' => 3,
            ],
            [
                'slug' => 'egift-card',
                'name' => 'eGift Cards',
                'tagline' => 'Your words in the scene',
                'description' => 'Personalised still eGift cards with your words hand-lettered into the scene. Perfect for birthdays, anniversaries and just-because gifts — delivered in 48 hours.',
                'sort_order' => 4,
            ],
            [
                'slug' => 'coloring-books',
                'name' => 'Coloring Books',
                'tagline' => 'Print at home · PDF',
                'description' => 'Printable Mango & Coco pages. Instant download.',
                'sort_order' => 5,
            ],
        ];

        foreach ($rows as $row) {
            Category::updateOrCreate(['slug' => $row['slug']], $row + ['is_active' => true]);
        }
    }
}
