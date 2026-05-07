<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // 'add_to_cart', 'checkout_initiated', 'purchase'
            $table->unsignedBigInteger('service_id')->nullable();
            $table->decimal('value', 10, 2)->nullable();
            $table->string('currency')->default('TND');
            $table->string('session_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            
            $table->index(['event_type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_analytics');
    }
};
