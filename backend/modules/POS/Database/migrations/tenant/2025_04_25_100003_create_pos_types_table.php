<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pos_types', function (Blueprint $table) {
            $table->id();
            $table->string('en_name');
            $table->string('ar_name')->nullable();
            $table->string('model')->default('Product');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_types');
    }
};
