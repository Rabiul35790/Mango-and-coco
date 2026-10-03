<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'site_name', 'logo_path', 'address',
        'contact_email', 'support_email',
        'copyright_text',
        'hero_video', 'hero_poster', 'hero_heading', 'hero_subheading',
        'hero_cta_label', 'hero_cta_url',
        'hero_secondary_cta_label', 'hero_secondary_cta_url',
        'meta_pixel_id', 'tiktok_pixel_id',
        'google_analytics_id', 'microsoft_clarity_id',
        'extra_head_scripts', 'extra_body_scripts',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'site_name' => 'Mango&Coco',
            'copyright_text' => '© {year} Mango&Coco. All artwork made with love.',
        ]);
    }

    public function copyright(): string
    {
        return str_replace('{year}', (string) date('Y'), $this->copyright_text ?? '');
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? '/storage/'.$this->logo_path : null;
    }
}
