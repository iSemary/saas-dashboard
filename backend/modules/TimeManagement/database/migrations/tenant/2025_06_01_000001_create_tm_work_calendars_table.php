<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tm_work_calendars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('timezone')->default('UTC');
            $table->json('working_days'); // e.g. [1,2,3,4,5]
            $table->json('holidays')->nullable(); // array of date strings
            $table->time('default_start_time')->default('09:00:00');
            $table->time('default_end_time')->default('17:00:00');
            $table->unsignedInteger('default_break_minutes')->default(60);
            $table->boolean('is_default')->default(false);
            $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tm_work_calendars');
    }
};
