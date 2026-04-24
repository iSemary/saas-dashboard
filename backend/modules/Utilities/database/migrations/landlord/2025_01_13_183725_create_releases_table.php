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
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->string('object_model');
            $table->unsignedBigInteger('object_id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('body');
            $table->string('version');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->dateTime('release_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
