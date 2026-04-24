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
        // Remove the php comments and put the comments in the ->comment() method
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g., "Kilogram", "Gram"');
            $table->string('code')->comment('e.g., "kg", "g"');
            $table->string('type_id')->comment('e.g., "weight", "length", "volume"');
            $table->decimal('base_conversion', 15, 5)->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_base_unit')->default(false)->comment('Whether this is a base unit (e.g., gram vs kilogram)');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
