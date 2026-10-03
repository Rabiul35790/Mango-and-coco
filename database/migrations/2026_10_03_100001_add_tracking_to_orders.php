<?php

use App\Support\TrackingId;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['orders', 'video_orders'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('tracking_id', 20)->nullable()->unique()->after('id');
                $t->dateTime('estimated_delivery_at')->nullable()->after('status');
            });
        }

        // Backfill existing rows so every order is trackable from day one.
        foreach (['orders', 'video_orders'] as $table) {
            $rows = DB::table($table)->whereNull('tracking_id')->get(['id']);
            foreach ($rows as $row) {
                DB::table($table)->where('id', $row->id)->update([
                    'tracking_id' => TrackingId::unique(),
                ]);
            }
        }
    }

    public function down(): void
    {
        foreach (['orders', 'video_orders'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropUnique(['tracking_id']);
                $t->dropColumn(['tracking_id', 'estimated_delivery_at']);
            });
        }
    }
};
