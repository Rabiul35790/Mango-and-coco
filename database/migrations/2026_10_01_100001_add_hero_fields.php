<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('hero_video', 500)->nullable()->after('hero_image');
            $table->string('hero_poster', 500)->nullable()->after('hero_video');
            $table->string('hero_heading', 255)->nullable()->after('hero_poster');
            $table->string('hero_subheading', 500)->nullable()->after('hero_heading');
            $table->string('hero_cta_label', 120)->nullable()->after('hero_subheading');
            $table->string('hero_cta_url', 500)->nullable()->after('hero_cta_label');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->string('hero_video', 500)->nullable()->after('logo_path');
            $table->string('hero_poster', 500)->nullable()->after('hero_video');
            $table->string('hero_heading', 255)->nullable()->after('hero_poster');
            $table->string('hero_subheading', 500)->nullable()->after('hero_heading');
            $table->string('hero_cta_label', 120)->nullable()->after('hero_subheading');
            $table->string('hero_cta_url', 500)->nullable()->after('hero_cta_label');
            $table->string('hero_secondary_cta_label', 120)->nullable()->after('hero_cta_url');
            $table->string('hero_secondary_cta_url', 500)->nullable()->after('hero_secondary_cta_label');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_video', 'hero_poster', 'hero_heading', 'hero_subheading',
                'hero_cta_label', 'hero_cta_url',
                'hero_secondary_cta_label', 'hero_secondary_cta_url',
            ]);
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'hero_video', 'hero_poster', 'hero_heading',
                'hero_subheading', 'hero_cta_label', 'hero_cta_url',
            ]);
        });
    }
};
