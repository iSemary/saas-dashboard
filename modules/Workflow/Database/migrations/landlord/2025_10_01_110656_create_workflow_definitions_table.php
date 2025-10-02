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
        Schema::create('workflow_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('module'); // crm, sales, etc.
            $table->string('trigger_event'); // lead.created, opportunity.updated, etc.
            $table->json('steps'); // Workflow steps configuration
            $table->json('conditions')->nullable(); // Trigger conditions
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['module', 'trigger_event']);
            $table->index(['is_active', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_definitions');
    }
};
