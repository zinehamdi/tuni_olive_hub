<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['oil','olive'])->index();
            // Expand to include common Tunisian varieties and quality grades used in tests
            $table->enum('variety', ['Chetoui','Chemlali','Oueslati','Meski','Zarrazi','Arbequina','Koroneiki','Sigoise','North','Kairouan','South'])->index();
            $table->enum('quality', ['EVOO','VIRGIN','LAMPANTE','premium','medium','foodservice'])->index();
            $table->boolean('is_organic')->default(false)->index();
            $table->decimal('volume_liters', 10, 2)->nullable();
            $table->decimal('weight_kg', 10, 2)->nullable();
            $table->decimal('price', 12, 3);
            $table->decimal('stock', 12, 3)->default(0);
            $table->json('media')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['seller_id','type','variety']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
