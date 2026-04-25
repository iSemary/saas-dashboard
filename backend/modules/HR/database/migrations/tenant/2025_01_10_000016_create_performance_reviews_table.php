<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_cycle_id')->constrained('performance_cycles')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('employees');
            $table->json('self_review')->nullable();
            $table->json('manager_review')->nullable();
            $table->json('peer_reviews')->nullable();
            $table->decimal('goals_achievement', 5, 2)->nullable();
            $table->text('strengths')->nullable();
            $table->text('improvements')->nullable();
            $table->decimal('overall_rating', 3, 1)->nullable(); // 1.0 to 5.0
            $table->string('status')->default('pending'); // pending, in_review, completed
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'status']);
            $table->index('performance_cycle_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};
