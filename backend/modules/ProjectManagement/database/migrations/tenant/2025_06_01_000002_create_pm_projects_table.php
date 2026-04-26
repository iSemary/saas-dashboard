<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('workspace_id')->nullable()->constrained('pm_workspaces')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('planning');
            $table->string('health')->default('on_track');
            $table->decimal('health_score', 5, 2)->default(100.00);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->decimal('spent', 15, 2)->default(0);
            $table->json('settings')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'workspace_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_projects');
    }
};
