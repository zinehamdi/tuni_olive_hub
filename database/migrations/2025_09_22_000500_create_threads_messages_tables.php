<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
    Schema::create('threads', function (Blueprint $table) {
            $table->id();
        $table->enum('object_type', ['order','listing','trip','load','export_offer','support','financing','contract'])->index();
            $table->unsignedBigInteger('object_id')->index();
            $table->json('participants')->nullable();
            $table->timestamps();
            $table->index(['object_type','object_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('threads')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body')->nullable();
            $table->json('attachments')->nullable();
            $table->boolean('is_flagged')->default(false)->index();
            $table->boolean('is_deleted')->default(false)->index();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('threads');
    }
};
