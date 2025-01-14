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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id()->comment('Primary key of the configurations table');

            // Configuration key-value pairs
            $table->string('configuration_key')->unique()->comment('Unique identifier key for the configuration setting');
            $table->text('configuration_value')->nullable()->comment('The value stored for this configuration setting');
            $table->text('description')->nullable()->comment('Optional description explaining the purpose of this configuration');

            // References and flags
            $table->unsignedBigInteger('type_id')->nullable()->comment('Reference to the configuration type, if applicable');
            $table->boolean('is_encrypted')->default(false)->comment('Indicates if the configuration value is stored in encrypted form');
            $table->boolean('is_system')->default(false)->comment('Indicates if this is a system-level configuration that should not be modified');
            $table->boolean('is_visible')->default(true)->comment('Controls whether this configuration should be visible in the UI');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('configuration_key');
            $table->index('type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
