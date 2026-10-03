<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->string('name', 160);
            $table->string('tagline', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('kind', 32)->index(); // sticker_pack | video | coloring_book | ecard
            $table->unsignedInteger('price_cents');
            $table->char('currency', 3)->default('USD');
            $table->string('image_key', 80)->default('hero');
            $table->string('lemon_variant_id', 60)->nullable()->index();
            $table->string('lemon_product_id', 60)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->timestamps();

            $table->index(['is_active', 'kind', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
