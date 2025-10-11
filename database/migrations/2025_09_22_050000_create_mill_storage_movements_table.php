<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mill_storage_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mill_id')->constrained('users')->cascadeOnDelete();
            $table->enum('product', ['olive','oil']);
            $table->enum('type', ['in','out','adjustment']);
            $table->decimal('qty', 12, 3);
            $table->enum('unit', ['kg','l']);
            $table->string('ref_object_type')->nullable();
            $table->unsignedBigInteger('ref_object_id')->nullable();
            $table->string('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['mill_id','product']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mill_storage_movements');
    }
};
