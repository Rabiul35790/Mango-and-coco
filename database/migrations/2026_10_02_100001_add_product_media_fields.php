<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Dynamic imagery (uploads win over the legacy image_key fallback).
            $table->string('cover_image', 500)->nullable()->after('image_key');
            // Social proof (admin sets from real reviews; hidden when count is 0).
            $table->decimal('rating', 2, 1)->nullable()->after('cover_image');
            $table->unsignedInteger('reviews_count')->default(0)->after('rating');
            // Compatibility chip groups, e.g. "Installs as a sticker pack" → [WhatsApp, Telegram].
            $table->string('platforms_a_label', 120)->nullable()->after('reviews_count');
            $table->json('platforms_a')->nullable()->after('platforms_a_label');
            $table->string('platforms_b_label', 120)->nullable()->after('platforms_a');
            $table->json('platforms_b')->nullable()->after('platforms_b_label');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'cover_image', 'rating', 'reviews_count',
                'platforms_a_label', 'platforms_a',
                'platforms_b_label', 'platforms_b',
            ]);
        });
    }
};
