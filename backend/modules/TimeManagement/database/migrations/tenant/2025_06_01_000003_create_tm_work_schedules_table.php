<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tm_work_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('work_calendar_id')->constrained('tm_work_calendars')->onDelete('cascade');
            $table->foreignUuid('shift_template_id')->nullable()->constrained('tm_shift_templates')->onDelete('set null');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->json('overrides')->nullable(); // per-day overrides
            $table->timestamps();

            $table->index(['tenant_id', 'user_id']);
            $table->index(['work_calendar_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tm_work_schedules');
    }
};
