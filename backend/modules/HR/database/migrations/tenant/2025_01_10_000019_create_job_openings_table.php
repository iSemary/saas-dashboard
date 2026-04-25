<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->string('location')->nullable();
            $table->string('type')->default('full-time'); // full-time, part-time, contract, internship
            $table->string('employment_type')->default('permanent'); // permanent, temporary
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('benefits')->nullable();
            $table->string('status')->default('draft'); // draft, published, closed, on_hold
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closes_at')->nullable();
            $table->foreignId('hiring_manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('recruiter_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->integer('vacancies')->default(1);
            $table->integer('filled_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('department_id');
            $table->index(['published_at', 'closes_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_openings');
    }
};
