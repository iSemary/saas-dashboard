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
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->foreignId('share_id')->nullable()->constrained('survey_shares')->nullOnDelete();
            $table->enum('respondent_type', ['anonymous', 'authenticated', 'email'])->default('anonymous');
            $table->foreignId('respondent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('respondent_email')->nullable();
            $table->string('respondent_name')->nullable();
            $table->enum('status', ['started', 'completed', 'partial', 'disqualified'])->default('started');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('time_spent_seconds')->nullable();
            $table->integer('score')->nullable();
            $table->integer('max_score')->nullable();
            $table->boolean('passed')->nullable();
            $table->string('resume_token', 64)->unique()->nullable();
            $table->string('locale', 10)->default('en');
            $table->json('custom_fields')->nullable();
            $table->timestamps();

            $table->index(['survey_id', 'status']);
            $table->index(['respondent_id', 'survey_id']);
            $table->index('respondent_email');
            $table->index('resume_token');
            $table->index('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
