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
        Schema::create('souk_prices', function (Blueprint $table) {
            $table->id();
            $table->string('souk_name'); // Sfax, Tunis, Sousse, Monastir, etc.
            $table->string('governorate'); // Governorate location
            $table->string('variety'); // chemlali, chetoui, etc.
            $table->enum('product_type', ['olive', 'oil'])->default('olive');
            $table->string('quality')->nullable(); // EVOO, virgin, lampante, premium, etc.
            $table->decimal('price_min', 10, 2); // Minimum price in souk
            $table->decimal('price_max', 10, 2); // Maximum price in souk
            $table->decimal('price_avg', 10, 2); // Average price
            $table->string('currency', 8)->default('TND');
            $table->string('unit', 16)->default('kg'); // kg, liter, ton
            $table->date('date'); // Price date
            $table->decimal('change_percentage', 5, 2)->nullable(); // % change from previous day
            $table->enum('trend', ['up', 'down', 'stable'])->default('stable');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index(['souk_name', 'date']);
            $table->index(['variety', 'date']);
            $table->index('date');
            $table->unique(['souk_name', 'variety', 'product_type', 'quality', 'date'], 'unique_souk_price_entry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('souk_prices');
    }
};
