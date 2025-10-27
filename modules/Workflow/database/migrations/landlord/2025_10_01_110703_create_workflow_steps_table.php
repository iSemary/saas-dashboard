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
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflow_instance_id');
            $table->integer('step_number');
            $table->string('step_type'); // action, condition, delay, etc.
            $table->string('step_name');
            $table->json('step_config'); // Step configuration
            $table->enum('status', ['pending', 'running', 'completed', 'failed', 'skipped'])->default('pending');
            $table->json('input_data')->nullable();
            $table->json('output_data')->nullable();
            $table->text('error_message')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['workflow_instance_id', 'step_number']);
            $table->index(['status', 'step_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
