<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['pending','authorized','captured','collected','failed'])->default('pending')->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'meta')) {
                $table->json('meta')->nullable()->after('escrow_id');
            }
        });

        Schema::table('loads', function (Blueprint $table) {
            if (!Schema::hasColumn('loads', 'order_id')) {
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete()->after('owner_id');
            }
            if (!Schema::hasColumn('loads', 'carrier_id')) {
                $table->foreignId('carrier_id')->nullable()->constrained('users')->nullOnDelete()->after('dropoff_addr_id');
            }
            if (!Schema::hasColumn('loads', 'eta_minutes')) {
                $table->integer('eta_minutes')->nullable()->after('carrier_id');
            }
        });

        Schema::table('trips', function (Blueprint $table) {
            if (!Schema::hasColumn('trips', 'pin_token')) {
                $table->string('pin_token')->nullable()->after('sr_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('orders', 'meta')) {
                $table->dropColumn('meta');
            }
        });

        Schema::table('loads', function (Blueprint $table) {
            if (Schema::hasColumn('loads', 'order_id')) {
                $table->dropConstrainedForeignId('order_id');
            }
            if (Schema::hasColumn('loads', 'carrier_id')) {
                $table->dropConstrainedForeignId('carrier_id');
            }
            if (Schema::hasColumn('loads', 'eta_minutes')) {
                $table->dropColumn('eta_minutes');
            }
        });

        Schema::table('trips', function (Blueprint $table) {
            if (Schema::hasColumn('trips', 'pin_token')) {
                $table->dropColumn('pin_token');
            }
        });
    }
};
