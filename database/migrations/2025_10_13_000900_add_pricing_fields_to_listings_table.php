<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings','price')) {
                $table->decimal('price', 12, 3)->nullable()->after('status');
            }
            if (!Schema::hasColumn('listings','currency')) {
                $table->string('currency', 8)->default('TND')->after('price');
            }
            if (!Schema::hasColumn('listings','quantity')) {
                $table->decimal('quantity', 12, 3)->nullable()->after('currency');
            }
            if (!Schema::hasColumn('listings','unit')) {
                $table->string('unit', 16)->nullable()->after('quantity');
            }
        });
    }
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (Schema::hasColumn('listings','unit')) $table->dropColumn('unit');
            if (Schema::hasColumn('listings','quantity')) $table->dropColumn('quantity');
            if (Schema::hasColumn('listings','currency')) $table->dropColumn('currency');
            if (Schema::hasColumn('listings','price')) $table->dropColumn('price');
        });
    }
};
