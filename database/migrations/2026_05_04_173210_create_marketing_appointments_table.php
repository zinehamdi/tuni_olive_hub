<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('marketing_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->text('business_info');
            $table->datetime('appointment_date');
            $table->json('cart_data');
            $table->decimal('total_budget', 10, 2);
            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('marketing_appointments');
    }
};
