<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('farm_name')->nullable();
            $table->string('location')->nullable();
            $table->string('company_name')->nullable();
            $table->integer('fleet_size')->nullable();
            $table->string('mill_name')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('packer_name')->nullable();
            $table->string('packaging_types')->nullable();
            $table->string('full_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'farm_name', 'location', 'company_name', 'fleet_size',
                'mill_name', 'capacity', 'packer_name', 'packaging_types', 'full_name'
            ]);
        });
    }
};
