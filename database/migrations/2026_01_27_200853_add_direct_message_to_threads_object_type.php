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
        if (DB::getDriverName() === 'sqlite') {
            // SQLite does not support ALTER ... MODIFY on enums; skip in tests
            return;
        }
        // Add 'direct_message' to the object_type enum
        DB::statement("ALTER TABLE threads MODIFY COLUMN object_type ENUM('order','listing','trip','load','export_offer','support','financing','contract','direct_message') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        // Remove 'direct_message' from the object_type enum
        DB::statement("ALTER TABLE threads MODIFY COLUMN object_type ENUM('order','listing','trip','load','export_offer','support','financing','contract') NOT NULL");
    }
};
