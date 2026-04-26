<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tm_attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('work_schedule_id')->nullable()->constrained('tm_work_schedules')->onDelete('set null');
            $table->date('date');
            $table->timestamp('clock_in_at')->nullable();
            $table->timestamp('clock_out_at')->nullable();
            $table->unsignedInteger('worked_minutes')->default(0);
            $table->unsignedInteger('break_minutes')->default(0);
            $table->unsignedInteger('overtime_minutes')->default(0);
            $table->string('status')->default('present'); // present, absent, late, half_day, holiday, leave
            $table->text('notes')->nullable();
            $table->json('location_data')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'user_id', 'date']);
            $table->unique(['user_id', 'date'], 'tm_attendances_date_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tm_attendances');
    }
};
