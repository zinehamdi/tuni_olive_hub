<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('marketing_services', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->string('title_fr')->nullable();
            $table->decimal('price_tnd_weekly', 10, 2);
            $table->string('currency')->default('TND');
            $table->string('icon_url')->nullable();
            $table->text('results_ar');
            $table->text('results_en')->nullable();
            $table->text('results_fr')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('marketing_services');
    }
};
