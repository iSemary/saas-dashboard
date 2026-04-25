<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('candidates');
            $table->string('type')->default('video'); // phone, video, in_person, panel
            $table->timestamp('scheduled_at');
            $table->integer('duration_minutes')->default(30);
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled, no_show
            $table->json('feedback')->nullable();
            $table->decimal('rating', 2, 1)->nullable(); // 1-5
            $table->string('recommendation')->nullable(); // hire, reject, second_interview
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['candidate_id', 'scheduled_at']);
            $table->index('status');
        });

        // Pivot table for interviewers
        Schema::create('interview_interviewer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained('interviews')->cascadeOnDelete();
            $table->foreignId('interviewer_id')->constrained('employees');
            $table->json('feedback')->nullable();
            $table->decimal('rating', 2, 1)->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_interviewer');
        Schema::dropIfExists('interviews');
    }
};
