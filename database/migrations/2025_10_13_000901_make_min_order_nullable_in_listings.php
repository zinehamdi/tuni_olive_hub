<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            // Make min_order nullable (it's optional for sellers)
            if (Schema::hasColumn('listings', 'min_order')) {
                $table->decimal('min_order', 12, 3)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (Schema::hasColumn('listings', 'min_order')) {
                $table->decimal('min_order', 12, 3)->nullable(false)->change();
            }
        });
    }
};
