<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('variety', 50)->change();
        });
    }
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('variety', ['Chetoui','Chemlali','Oueslati','Meski','Zarrazi','Arbequina','Koroneiki','Sigoise','North','Kairouan','South'])->change();
        });
    }
};
