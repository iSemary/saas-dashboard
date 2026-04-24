<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brand_module', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->unsignedBigInteger('module_id'); // No foreign key constraint since modules table is in landlord DB
            $table->timestamps();

            // Ensure unique brand-module combinations
            $table->unique(['brand_id', 'module_id']);
            
            // Indexes for performance
            $table->index('brand_id');
            $table->index('module_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_module');
    }
};
