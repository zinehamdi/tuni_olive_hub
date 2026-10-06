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
        Schema::table('listings', function (Blueprint $table) {
            $table->foreignId('address_id')->nullable()->after('seller_id')->constrained('addresses')->nullOnDelete();
        });

        // Backfill address_id for existing listings from seller's first address if available
        $listings = DB::table('listings')->select('id', 'seller_id')->get();
        foreach ($listings as $listing) {
            $address = DB::table('addresses')->where('user_id', $listing->seller_id)->first();
            if ($address) {
                DB::table('listings')->where('id', $listing->id)->update(['address_id' => $address->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropColumn('address_id');
        });
    }
};
