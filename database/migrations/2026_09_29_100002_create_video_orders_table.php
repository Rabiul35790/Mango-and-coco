<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_orders', function (Blueprint $table) {
            $table->id();
            $table->string('product_slug', 120)->index();
            $table->string('customer_name', 120);
            $table->string('customer_email', 200)->index();
            $table->string('recipient_name', 120)->nullable();
            $table->string('occasion', 120)->nullable();
            $table->string('message', 400);
            $table->text('notes')->nullable();
            $table->string('status', 32)->default('new')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_orders');
    }
};
