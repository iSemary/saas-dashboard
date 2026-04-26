<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_task_dependencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('predecessor_id')->constrained('pm_tasks')->onDelete('cascade');
            $table->foreignUuid('successor_id')->constrained('pm_tasks')->onDelete('cascade');
            $table->string('type')->default('finish_to_start');
            $table->timestamps();

            $table->unique(['predecessor_id', 'successor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_task_dependencies');
    }
};
