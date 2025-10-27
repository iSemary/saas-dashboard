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
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('module'); // crm, sales, etc.
            $table->string('category'); // sales, marketing, finance, etc.
            $table->json('template_config'); // Template configuration
            $table->json('default_filters')->nullable();
            $table->json('default_columns')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['module', 'category']);
            $table->index(['is_system', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_templates');
    }
};
