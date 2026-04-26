<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_opening_id')->constrained('hr_job_openings')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('hr_candidates')->cascadeOnDelete();
            $table->foreignId('pipeline_stage_id')->nullable()->constrained('hr_pipeline_stages')->nullOnDelete();
            $table->string('status')->default('new'); // new, screening, interview, offer, hired, rejected
            $table->timestamp('applied_at');
            $table->text('cover_letter')->nullable();
            $table->json('answers')->nullable();
            $table->string('source')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('salary_expectation', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->date('available_from')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['job_opening_id', 'status']);
            $table->index(['candidate_id', 'status']);
            $table->index('pipeline_stage_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_applications');
    }
};
