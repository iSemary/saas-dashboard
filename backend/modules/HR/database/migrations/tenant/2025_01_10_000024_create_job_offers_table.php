<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_job_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('hr_applications')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('hr_candidates');
            $table->foreignId('job_opening_id')->constrained('hr_job_openings');
            $table->decimal('salary', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('bonus', 12, 2)->default(0);
            $table->json('benefits')->nullable();
            $table->date('start_date');
            $table->date('expiry_date');
            $table->string('status')->default('draft'); // draft, sent, accepted, rejected, expired
            $table->json('terms')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(['candidate_id', 'job_opening_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_job_offers');
    }
};
