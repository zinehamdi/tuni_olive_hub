<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('loads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->enum('kind', ['oil','olive'])->index();
            $table->decimal('qty', 12, 3);
            $table->enum('unit', ['l','kg','ton']);
            $table->foreignId('pickup_addr_id')->constrained('addresses');
            $table->foreignId('dropoff_addr_id')->constrained('addresses');
            $table->timestamp('deadline_at')->nullable();
            $table->decimal('price_floor', 12, 3)->nullable();
            $table->decimal('price_ceiling', 12, 3)->nullable();
            $table->enum('status', ['new','matched','in_transit','delivered','settled'])->default('new')->index();
            $table->timestamps();
        });

        Schema::create('carrier_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('load_id')->constrained('loads')->cascadeOnDelete();
            $table->foreignId('carrier_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('offer_price', 12, 3);
            $table->integer('eta_minutes');
            $table->enum('status', ['proposed','accepted','declined','expired'])->default('proposed')->index();
            $table->timestamps();
            $table->unique(['load_id','carrier_id']);
        });

        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrier_id')->constrained('users')->cascadeOnDelete();
            $table->json('load_ids')->nullable();
            $table->longText('route_polyline')->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->decimal('distance_km', 10, 2)->nullable();
            $table->decimal('earning', 12, 3)->nullable();
            $table->string('sr_code')->index();
            $table->timestamps();
        });

        Schema::create('pods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->json('pickup_photos')->nullable();
            $table->json('dropoff_photos')->nullable();
            $table->string('signed_name')->nullable();
            $table->string('signed_pin')->nullable();
            $table->string('qr_token')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pods');
        Schema::dropIfExists('trips');
        Schema::dropIfExists('carrier_offers');
        Schema::dropIfExists('loads');
    }
};
