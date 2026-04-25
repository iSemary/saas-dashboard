<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('one_on_ones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('manager_id')->constrained('employees');
            $table->timestamp('scheduled_at');
            $table->integer('duration_minutes')->default(30);
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled
            $table->json('talking_points')->nullable();
            $table->text('notes')->nullable();
            $table->json('action_items')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'scheduled_at']);
            $table->index(['manager_id', 'scheduled_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('one_on_ones');
    }
};
