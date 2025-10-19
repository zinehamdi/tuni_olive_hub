<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trust_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->json('meta')->nullable();
            $table->integer('weight');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id','type']);
        });

        Schema::table('financings', function (Blueprint $table) {
            $table->decimal('delivered_qty', 12, 3)->default(0)->after('qty_target');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false)->after('media');
            $table->boolean('export_ready')->default(false)->after('is_premium');
            $table->json('certs')->nullable()->after('export_ready');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_premium','export_ready','certs']);
        });
        Schema::table('financings', function (Blueprint $table) {
            $table->dropColumn(['delivered_qty']);
        });
        Schema::dropIfExists('trust_events');
    }
};
