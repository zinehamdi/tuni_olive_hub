<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','camion_capacity')) {
                $table->integer('camion_capacity')->nullable();
            }
            if (!Schema::hasColumn('users','mill_name')) {
                $table->string('mill_name')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users','camion_capacity')) {
                $table->dropColumn('camion_capacity');
            }
            if (Schema::hasColumn('users','mill_name')) {
                $table->dropColumn('mill_name');
            }
        });
    }
};
