<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add is_hidden to messages
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'is_hidden')) {
                $table->boolean('is_hidden')->default(false)->index();
            }
        });

        // Extend reports table with moderator fields and object_type enum normalization
        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'moderator_id')) {
                $table->foreignId('moderator_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('reports', 'note')) {
                $table->string('note')->nullable()->after('moderator_id');
            }
        });

        // Financing risk events
        Schema::create('financing_risk_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financing_id')->constrained('financings')->cascadeOnDelete();
            $table->enum('type', ['late_delivery','short_qty','carrier_default']);
            $table->integer('weight');
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['financing_id','type']);
        });

        // Mill batches
        Schema::create('mill_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mill_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('olive_qty_kg', 12, 3);
            $table->decimal('oil_qty_l', 12, 3)->nullable();
            $table->decimal('extraction_rate', 5, 4)->nullable();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
            $table->index(['mill_id','started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mill_batches');
        Schema::dropIfExists('financing_risk_events');
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'note')) $table->dropColumn('note');
            if (Schema::hasColumn('reports', 'moderator_id')) $table->dropConstrainedForeignId('moderator_id');
        });
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'is_hidden')) $table->dropColumn('is_hidden');
        });
    }
};
