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
            $table->boolean('is_featured')->default(false)->after('status');
            $table->integer('tree_count')->nullable()->after('is_featured');
            $table->string('sale_mode')->nullable()->after('tree_count'); // 'grain' (زيتون حب) or 'saniya' (سانية للخضارة)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'tree_count', 'sale_mode']);
        });
    }
};
