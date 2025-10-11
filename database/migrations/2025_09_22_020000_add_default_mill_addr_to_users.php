<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'default_mill_addr_id')) {
                $table->foreignId('default_mill_addr_id')->nullable()->constrained('addresses')->nullOnDelete();
            }
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'default_mill_addr_id')) {
                $table->dropConstrainedForeignId('default_mill_addr_id');
            }
        });
    }
};
