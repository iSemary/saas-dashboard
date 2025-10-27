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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // chart, table, summary, etc.
            $table->string('module'); // crm, sales, etc.
            $table->json('query'); // Report query configuration
            $table->json('filters'); // Report filters
            $table->json('columns'); // Report columns configuration
            $table->json('chart_config')->nullable(); // Chart configuration
            $table->boolean('is_scheduled')->default(false);
            $table->string('schedule_frequency')->nullable(); // daily, weekly, monthly
            $table->json('schedule_config')->nullable();
            $table->boolean('is_public')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['module', 'type']);
            $table->index(['created_by', 'is_public']);
            $table->index('is_scheduled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
