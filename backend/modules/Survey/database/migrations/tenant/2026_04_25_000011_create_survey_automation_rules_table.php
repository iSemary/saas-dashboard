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
        Schema::create('survey_automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('survey_surveys')->cascadeOnDelete();
            $table->string('name');
            $table->enum('trigger_type', [
                'response_created', 'response_completed', 'question_answered', 'survey_closed', 'score_reached'
            ]);
            $table->json('conditions')->nullable();
            $table->enum('action_type', [
                'send_email', 'update_field', 'create_activity',
                'send_notification', 'trigger_webhook', 'create_crm_activity'
            ]);
            $table->json('action_config');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['survey_id', 'is_active']);
            $table->index('trigger_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_automation_rules');
    }
};
