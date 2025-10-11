<?php
/**
 * Migration: Add expected_eta and actual_eta to trips
 * إضافة حقول التوقيت المتوقع والفعلي للرحلات
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->timestamp('expected_eta')->nullable();
            $table->timestamp('actual_eta')->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['expected_eta', 'actual_eta']);
        });
    }
};
