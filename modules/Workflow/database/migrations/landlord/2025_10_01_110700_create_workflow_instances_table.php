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
        Schema::create('workflow_instances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflow_definition_id');
            $table->morphs('related'); // related_id, related_type
            $table->enum('status', ['running', 'completed', 'failed', 'cancelled'])->default('running');
            $table->integer('current_step')->default(0);
            $table->json('context')->nullable(); // Workflow execution context
            $table->json('variables')->nullable(); // Workflow variables
            $table->dateTime('started_at');
            $table->dateTime('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['workflow_definition_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_instances');
    }
};
