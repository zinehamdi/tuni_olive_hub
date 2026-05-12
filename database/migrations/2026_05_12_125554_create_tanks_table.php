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
        Schema::create('tanks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., Tank 1, North Field
            $table->decimal('capacity', 12, 2); // Max capacity
            $table->decimal('current_volume', 12, 2); // Current volume
            $table->string('type'); // 'olive' or 'oil'
            $table->string('variety')->nullable(); // Chemlali, etc.
            $table->decimal('acidity', 5, 2)->nullable(); // Acidity level in percentage
            $table->string('quality')->nullable(); // Extra Virgin, Virgin, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanks');
    }
};
