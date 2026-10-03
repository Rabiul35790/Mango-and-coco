<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'slug', 'name', 'tagline', 'description',
        'hero_image', 'hero_video', 'hero_poster',
        'hero_heading', 'hero_subheading', 'hero_cta_label', 'hero_cta_url',
        'is_active', 'sort_order',
        'meta_title', 'meta_description',
        'details_heading', 'details_body',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->ordered();
    }

    /** Flow driver: stickers|wallpaper => instant download details page; video|egift-card => wizard */
    public function flow(): string
    {
        return match ($this->slug) {
            'video', 'egift-card' => 'wizard',
            default => 'details',
        };
    }

    public function requiresDownload(): bool
    {
        return in_array($this->slug, ['stickers', 'wallpaper', 'coloring-books'], true);
    }
}
