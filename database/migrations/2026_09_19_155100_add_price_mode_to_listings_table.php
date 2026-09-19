<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('listings', 'price_mode')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->string('price_mode', 32)->nullable()->default('per_unit')->after('price'); // 'per_unit' or 'whole'
            });
        }

        // Migrate existing listings with unit = 'سانية' or saniya packaging
        DB::table('listings')
            ->where('unit', 'سانية')
            ->orWhere('unit', 'saniya')
            ->update([
                'sale_mode' => 'saniya',
                'price_mode' => 'whole',
                'unit' => 'ton',
            ]);

        DB::table('listings')
            ->where('packaging', 'LIKE', '%سانية%')
            ->whereNull('sale_mode')
            ->update([
                'sale_mode' => 'saniya',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('listings', 'price_mode')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->dropColumn('price_mode');
            });
        }
    }
};
