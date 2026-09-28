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
        Schema::table('transporter_requests', function (Blueprint $table) {
            $table->string('pickup_delegation', 100)->nullable()->after('pickup_governorate');
            $table->string('dropoff_delegation', 100)->nullable()->after('dropoff_governorate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transporter_requests', function (Blueprint $table) {
            $table->dropColumn(['pickup_delegation', 'dropoff_delegation']);
        });
    }
};
