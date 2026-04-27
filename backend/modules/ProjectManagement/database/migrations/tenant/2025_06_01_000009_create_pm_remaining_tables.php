<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_project_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->foreignUuid('project_id')->constrained('pm_projects')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('role')->default('member');
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
            $table->index(['tenant_id', 'project_id']);
        });

        Schema::create('pm_project_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('structure')->nullable();
            $table->boolean('is_public')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['tenant_id', 'is_public']);
        });

        Schema::create('pm_risks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->foreignUuid('project_id')->constrained('pm_projects')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('level')->default('medium');
            $table->string('status')->default('identified');
            $table->text('mitigation')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'project_id']);
        });

        Schema::create('pm_issues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->foreignUuid('project_id')->constrained('pm_projects')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('open');
            $table->string('priority')->default('medium');
            $table->foreignId('reporter_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assignee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'project_id']);
        });

        Schema::create('pm_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->foreignUuid('project_id')->constrained('pm_projects')->onDelete('cascade');
            $table->morphs('commentable');
            $table->text('content');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['tenant_id', 'project_id']);
        });

        Schema::create('pm_sprint_cycles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->foreignUuid('project_id')->constrained('pm_projects')->onDelete('cascade');
            $table->string('name');
            $table->string('status')->default('planning');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['tenant_id', 'project_id']);
        });

        Schema::create('pm_webhooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->foreignUuid('project_id')->constrained('pm_projects')->onDelete('cascade');
            $table->string('name');
            $table->string('url');
            $table->json('events')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('secret')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['tenant_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_webhooks');
        Schema::dropIfExists('pm_sprint_cycles');
        Schema::dropIfExists('pm_comments');
        Schema::dropIfExists('pm_issues');
        Schema::dropIfExists('pm_risks');
        Schema::dropIfExists('pm_project_templates');
        Schema::dropIfExists('pm_project_members');
    }
};
