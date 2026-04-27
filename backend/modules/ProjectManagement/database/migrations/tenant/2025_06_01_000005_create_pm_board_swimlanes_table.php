<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_board_swimlanes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->foreignUuid('project_id')->constrained('pm_projects')->onDelete('cascade');
            $table->string('name');
            $table->string('type')->default('custom');
            $table->string('value')->nullable();
            $table->double('position', 16, 6)->default(0);
            $table->timestamps();

            $table->index(['tenant_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_board_swimlanes');
    }
};
