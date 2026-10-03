<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('name', 160);
            $table->string('tagline', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('hero_image', 255)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('details_heading', 255)->nullable();
            $table->text('details_body')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name', 160)->default('Mango&Coco');
            $table->string('logo_path', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('contact_email', 200)->nullable();
            $table->string('support_email', 200)->nullable();
            $table->text('copyright_text')->nullable();
            $table->string('meta_pixel_id', 60)->nullable();
            $table->string('tiktok_pixel_id', 60)->nullable();
            $table->string('google_analytics_id', 60)->nullable();
            $table->string('microsoft_clarity_id', 60)->nullable();
            $table->text('extra_head_scripts')->nullable();
            $table->text('extra_body_scripts')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('categories');
    }
};
