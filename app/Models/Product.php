<?php

namespace App\Models;

use App\Enums\ProductKind;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'slug', 'name', 'tagline', 'description', 'kind', 'category_id',
        'price_cents', 'currency', 'image_key', 'cover_image',
        'rating', 'reviews_count',
        'platforms_a_label', 'platforms_a', 'platforms_b_label', 'platforms_b',
        'gallery', 'demo_video_url', 'demo_video_file', 'demo_video_poster',
        'instructions', 'usage_steps', 'whats_included',
        'download_disk', 'download_path', 'delivery_type',
        'lemon_variant_id', 'lemon_product_id',
        'is_active', 'sort_order',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'kind' => ProductKind::class,
        'price_cents' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'gallery' => 'array',
        'usage_steps' => 'array',
        'platforms_a' => 'array',
        'platforms_b' => 'array',
        'rating' => 'float',
        'reviews_count' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(ProductTemplate::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('name');
    }

    public function scopeOfKind(Builder $q, ?ProductKind $kind): Builder
    {
        return $kind ? $q->where('kind', $kind) : $q;
    }

    public function scopeOfCategory(Builder $q, ?Category $category): Builder
    {
        return $category ? $q->where('category_id', $category->id) : $q;
    }

    public function categorySlug(): ?string
    {
        return $this->category?->slug;
    }

    /** Wizard flow for video + egift-card categories; everything else is details+buy. */
    public function usesWizard(): bool
    {
        return in_array($this->categorySlug(), ['video', 'egift-card'], true)
            || $this->delivery_type === 'custom';
    }

    public function hasDownload(): bool
    {
        return filled($this->download_path);
    }

    /** Secure, short-lived download URL (R2 private by default). */
    public function temporaryDownloadUrl(int $minutes = 15): ?string
    {
        if (! $this->hasDownload()) {
            return null;
        }

        $disk = $this->download_disk ?: 'r2';

        try {
            return Storage::disk($disk)->temporaryUrl($this->download_path, now()->addMinutes($minutes));
        } catch (\Throwable) {
            // Local fallback (dev without R2): public/storage path
            if ($disk === 'public' || $disk === 'local') {
                return Storage::disk($disk)->url($this->download_path);
            }

            return null;
        }
    }

    public function formattedPrice(): string
    {
        return number_format($this->price_cents / 100, 2).' '.strtoupper($this->currency);
    }
}
