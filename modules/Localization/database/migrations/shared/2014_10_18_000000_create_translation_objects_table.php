<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('translation_objects', function (Blueprint $table) {
            $table->id();
            $table->string('object_type')->comment("App\Models\Category");
            $table->unsignedBigInteger('object_id')->comment("ID of the object");
            $table->unsignedBigInteger('translation_id')->comment("ID from the translations table");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_objects');
    }
};
