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
        Schema::create('world_olive_prices', function (Blueprint $table) {
            $table->id();
            $table->string('country'); // Spain, Italy, Greece, Turkey, Tunisia, etc.
            $table->string('region')->nullable(); // Andalusia, Tuscany, Crete, etc.
            $table->string('variety')->nullable(); // Arbequina, Koroneiki, Picual, etc.
            $table->enum('quality', ['EVOO', 'virgin', 'refined', 'lampante'])->default('EVOO');
            $table->decimal('price', 10, 2);
            $table->string('currency', 8)->default('EUR');
            $table->string('unit', 16)->default('liter');
            $table->date('date');
            $table->decimal('change_percentage', 5, 2)->nullable();
            $table->enum('trend', ['up', 'down', 'stable'])->default('stable');
            $table->string('source')->nullable(); // IOC, market data, manual
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['country', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('world_olive_prices');
    }
};
