<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transporter_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('transporter_requests', 'cargo_type')) {
                $table->string('cargo_type', 20)->default('oil')->after('unit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transporter_requests', function (Blueprint $table) {
            if (Schema::hasColumn('transporter_requests', 'cargo_type')) {
                $table->dropColumn('cargo_type');
            }
        });
    }
};
