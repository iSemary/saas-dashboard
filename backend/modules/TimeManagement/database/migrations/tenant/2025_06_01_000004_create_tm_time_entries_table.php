<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tm_time_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('project_id')->nullable(); // cross-link to pm_projects
            $table->foreignUuid('task_id')->nullable(); // cross-link to pm_tasks
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->string('source')->default('manual'); // manual, timer, calendar
            $table->boolean('is_billable')->default(true);
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft, submitted, approved, rejected
            $table->uuid('timesheet_id')->nullable();
            $table->uuid('time_session_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'user_id', 'date']);
            $table->index(['project_id']);
            $table->index(['timesheet_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tm_time_entries');
    }
};
