<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tm_time_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('project_id')->nullable();
            $table->foreignUuid('task_id')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('stopped_at')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->boolean('is_running')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'user_id', 'is_running']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tm_time_sessions');
    }
};
