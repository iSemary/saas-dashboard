<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->foreignUuid('project_id')->constrained('pm_projects')->onDelete('cascade');
            $table->foreignUuid('milestone_id')->nullable()->constrained('pm_milestones')->onDelete('set null');
            $table->foreignUuid('board_column_id')->nullable()->constrained('pm_board_columns')->onDelete('set null');
            $table->foreignUuid('swimlane_id')->nullable()->constrained('pm_board_swimlanes')->onDelete('set null');
            $table->foreignUuid('parent_task_id')->nullable()->constrained('pm_tasks')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('todo');
            $table->string('priority')->default('medium');
            $table->string('type')->default('task');
            $table->double('position', 16, 6)->default(0);
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->default(0);
            $table->foreignId('assignee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'project_id']);
            $table->index(['tenant_id', 'board_column_id']);
            $table->index(['project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_tasks');
    }
};
