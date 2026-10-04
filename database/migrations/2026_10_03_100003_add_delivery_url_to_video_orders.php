<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('video_orders', function (Blueprint $table) {
            // Admin pastes the finished file link (Drive/Dropbox/CDN).
            // Included in the "delivered" email + track page button.
            $table->string('delivery_url', 500)->nullable()->after('checkout_url');
        });
    }

    public function down(): void
    {
        Schema::table('video_orders', function (Blueprint $table) {
            $table->dropColumn('delivery_url');
        });
    }
};
