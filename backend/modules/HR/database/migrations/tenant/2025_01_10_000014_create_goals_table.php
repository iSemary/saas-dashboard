<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_cycle_id')->constrained('hr_performance_cycles')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // professional_development, project_delivery, etc.
            $table->string('status')->default('draft'); // draft, active, completed, cancelled
            $table->decimal('progress', 5, 2)->default(0);
            $table->decimal('weight', 5, 2)->default(1);
            $table->date('start_date');
            $table->date('due_date');
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'status']);
            $table->index('performance_cycle_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_goals');
    }
};
