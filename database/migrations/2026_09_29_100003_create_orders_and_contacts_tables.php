<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('lemon_order_id', 80)->nullable()->unique();
            $table->string('product_slug', 120)->index();
            $table->string('variant_id', 60)->nullable()->index();
            $table->string('customer_email', 200)->index();
            $table->string('customer_name', 160)->nullable();
            $table->unsignedInteger('total_cents')->default(0);
            $table->char('currency', 3)->default('USD');
            $table->string('status', 32)->default('pending')->index();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 200)->index();
            $table->string('subject', 160)->nullable();
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('contact_messages');
    }
};
