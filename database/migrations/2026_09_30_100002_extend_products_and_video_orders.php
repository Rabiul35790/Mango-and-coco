<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('kind')->constrained()->nullOnDelete();
            // Rich details page content (admin-controlled, no code changes)
            $table->json('gallery')->nullable()->after('image_key');
            $table->string('demo_video_url', 500)->nullable()->after('gallery');
            $table->string('demo_video_poster', 500)->nullable();
            $table->text('instructions')->nullable();
            $table->json('usage_steps')->nullable();
            $table->text('whats_included')->nullable();
            // Deliverable file (zip on Cloudflare R2)
            $table->string('download_disk', 20)->default('r2');
            $table->string('download_path', 500)->nullable();
            $table->string('delivery_type', 20)->default('instant')->after('download_path'); // instant|custom
            $table->index(['category_id', 'is_active', 'sort_order']);
        });

        Schema::table('video_orders', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('format_label', 160)->nullable()->after('product_slug');
            $table->string('deliver_to', 10)->default('self')->after('occasion'); // self|other
            $table->string('recipient_email', 200)->nullable()->after('recipient_name');
            $table->string('checkout_url', 500)->nullable();
            $table->string('lemon_order_id', 80)->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('video_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn(['format_label', 'deliver_to', 'recipient_email', 'checkout_url', 'lemon_order_id']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn(['gallery', 'demo_video_url', 'demo_video_poster', 'instructions', 'usage_steps', 'whats_included', 'download_disk', 'download_path', 'delivery_type']);
        });
    }
};
