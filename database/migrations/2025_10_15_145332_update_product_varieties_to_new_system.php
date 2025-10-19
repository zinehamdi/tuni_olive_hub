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
        // Mapping of old variety names to new standardized names
        $varietyMapping = [
            // Existing varieties -> New standardized names
            'Chemlali Olive Oil' => 'chemlali',
            'Chemlali Olives' => 'chemlali',
            'Chetoui Olive Oil' => 'chetoui',
            'Chetoui Olives' => 'chetoui',
            'Meski Olives' => 'meski',
            'Cold Pressed Olive Oil' => 'chemlali', // Default to chemlali (most common)
            'Extra Virgin Olive Oil' => 'chemlali', // Default to chemlali
            'Premium Blend Olive Oil' => 'blend',
            'Organic Extra Virgin' => 'chemlali', // Default to chemlali
            'Fresh Olives' => 'meski', // Default to meski
            'Table Olives' => 'meski', // Default to meski
        ];

        foreach ($varietyMapping as $oldVariety => $newVariety) {
            DB::table('products')
                ->where('variety', $oldVariety)
                ->update(['variety' => $newVariety]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse mapping - restore original variety names
        $reverseMapping = [
            'chemlali' => 'Chemlali Olive Oil',
            'chetoui' => 'Chetoui Olive Oil',
            'meski' => 'Meski Olives',
            'blend' => 'Premium Blend Olive Oil',
        ];

        foreach ($reverseMapping as $newVariety => $oldVariety) {
            DB::table('products')
                ->where('variety', $newVariety)
                ->update(['variety' => $oldVariety]);
        }
    }
};
