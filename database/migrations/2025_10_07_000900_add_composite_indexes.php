<?php
/**
 * Composite Indexes Migration
 * إضافة فهارس مركبة لتحسين الأداء
 * Adds composite indexes for orders, listings, trips
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['seller_id', 'status'], 'orders_seller_status_idx');
        });
        Schema::table('listings', function (Blueprint $table) {
            $table->index(['seller_id', 'status'], 'listings_seller_status_idx');
        });
        // Removed index on trips.status (column does not exist)
    }
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_seller_status_idx');
        });
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex('listings_seller_status_idx');
        });
        Schema::table('trips', function (Blueprint $table) {
            $table->dropIndex('trips_carrier_status_idx');
        });
    }
};
