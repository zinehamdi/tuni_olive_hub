<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loads', function (Blueprint $table) {
            if (!Schema::hasColumn('loads', 'meta')) {
                $table->json('meta')->nullable()->after('eta_minutes');
            }
        });

        Schema::table('trips', function (Blueprint $table) {
            if (!Schema::hasColumn('trips', 'pin_hash')) {
                $table->string('pin_hash')->nullable()->after('sr_code');
            }
            if (!Schema::hasColumn('trips', 'pin_qr')) {
                $table->string('pin_qr')->nullable()->after('pin_hash');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loads', function (Blueprint $table) {
            if (Schema::hasColumn('loads', 'meta')) {
                $table->dropColumn('meta');
            }
        });
        Schema::table('trips', function (Blueprint $table) {
            if (Schema::hasColumn('trips', 'pin_hash')) {
                $table->dropColumn('pin_hash');
            }
            if (Schema::hasColumn('trips', 'pin_qr')) {
                $table->dropColumn('pin_qr');
            }
        });
    }
};
