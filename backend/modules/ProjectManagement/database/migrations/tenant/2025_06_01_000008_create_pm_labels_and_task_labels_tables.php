<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_labels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('project_id')->constrained('pm_projects')->onDelete('cascade');
            $table->string('name');
            $table->string('color')->default('#6B7280');
            $table->timestamps();

            $table->index(['tenant_id', 'project_id']);
        });

        Schema::create('pm_task_labels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_id')->constrained('pm_tasks')->onDelete('cascade');
            $table->foreignUuid('label_id')->constrained('pm_labels')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['task_id', 'label_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_task_labels');
        Schema::dropIfExists('pm_labels');
    }
};
