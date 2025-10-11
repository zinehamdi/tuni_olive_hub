<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('export_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('variety');
            $table->text('spec')->nullable();
            $table->decimal('qty_tons', 12, 3);
            $table->enum('incoterm', ['fob','cif']);
            $table->string('port_from');
            $table->string('port_to');
            $table->enum('currency', ['usd','eur']);
            $table->decimal('unit_price', 12, 3);
            $table->json('docs')->nullable();
            $table->enum('status', ['review','active','negotiating','contracted','shipping','completed'])->default('review')->index();
            $table->timestamps();
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_offer_id')->constrained('export_offers')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('payment_term', ['lc','tt']);
            $table->boolean('escrow')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->enum('status', ['draft','sent','signed','funded','shipping','closed'])->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('prices_daily', function (Blueprint $table) {
            $table->date('date')->primary();
            $table->decimal('global_oil_usd_ton', 12, 2)->nullable();
            $table->decimal('tunis_baz_tnd_kg', 12, 3)->nullable();
            $table->decimal('organic_tnd_l', 12, 3)->nullable();
            $table->json('by_governorate_json')->nullable();
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('target_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('object_type')->nullable();
            $table->unsignedBigInteger('object_id')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->json('photos')->nullable();
            $table->boolean('is_verified_purchase')->default(false)->index();
            $table->boolean('is_visible')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
            $table->foreignId('replier_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->string('object_type');
            $table->unsignedBigInteger('object_id');
            $table->string('reason');
            $table->json('evidence')->nullable();
            $table->enum('status', ['new','reviewing','resolved','rejected'])->default('new')->index();
            $table->timestamps();
            $table->index(['object_type','object_id']);
        });

        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('blocked_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['blocker_id','blocked_user_id']);
        });

        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('target_id')->constrained('users')->cascadeOnDelete();
            $table->string('object_type');
            $table->unsignedBigInteger('object_id');
            $table->string('reason');
            $table->json('evidence')->nullable();
            $table->enum('status', ['new','mediating','closed'])->default('new')->index();
            $table->timestamps();
            $table->index(['object_type','object_id']);
        });

        Schema::create('financings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('carrier_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('qty_target', 12, 3)->nullable();
            $table->decimal('amount', 12, 3);
            $table->decimal('price_per_kg', 12, 3)->nullable();
            $table->text('terms')->nullable();
            $table->enum('status', ['proposed','active','settled','defaulted'])->default('proposed')->index();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('object_type')->nullable();
            $table->unsignedBigInteger('object_id')->nullable();
            $table->string('ip')->nullable();
            $table->string('ua')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['object_type','object_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('financings');
        Schema::dropIfExists('disputes');
        Schema::dropIfExists('blocks');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('review_replies');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('prices_daily');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('export_offers');
    }
};
