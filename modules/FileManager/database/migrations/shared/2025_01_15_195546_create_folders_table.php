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
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Name of the folder');
            $table->text('description')->nullable()->comment('Optional description of the folder');
            $table->foreignId('parent_id')->nullable()->constrained('folders')->nullOnDelete()->comment('ID of the parent folder, null if root folder');
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active')->comment('Current status of the file (e.g., active, inactive, archived)');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folders');
    }
};
