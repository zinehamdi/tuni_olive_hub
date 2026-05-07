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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['demand', 'service', 'supply']); // demand = searching, service = providing, supply = supplier
            $table->json('title'); // {ar: "...", fr: "...", en: "..."}
            $table->json('description');
            $table->string('price_range')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['active', 'expired', 'closed'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
