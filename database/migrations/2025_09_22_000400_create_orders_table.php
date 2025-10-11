<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->decimal('qty', 12, 3);
            $table->enum('unit', ['l','kg','ton']);
            $table->decimal('price_unit', 12, 3);
            $table->decimal('total', 12, 3);
            $table->enum('payment_method', ['cod','flouci','d17','stripe','bank_lc']);
            $table->enum('status', ['pending','confirmed','ready','shipping','delivered','cancelled'])->default('pending')->index();
            $table->string('escrow_id')->nullable();
            $table->timestamps();
            $table->index(['buyer_id','seller_id','status']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
