<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('carriers', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->string('vehicle_type')->nullable();
            $table->integer('capacity_liters')->nullable();
            $table->json('preferred_govs')->nullable();
            $table->json('docs')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('carriers');
    }
};
