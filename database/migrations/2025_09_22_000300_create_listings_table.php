<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['draft','active','paused','sold','out'])->default('draft')->index();
            $table->decimal('min_order', 12, 3)->default(0);
            $table->json('payment_methods')->nullable();
            $table->json('delivery_options')->nullable();
            $table->timestamps();
            $table->index(['seller_id','status']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
