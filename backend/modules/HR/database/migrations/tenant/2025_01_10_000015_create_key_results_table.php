<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_key_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained('hr_goals')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('target_value', 12, 2);
            $table->decimal('current_value', 12, 2)->default(0);
            $table->string('unit'); // percentage, count, currency, etc.
            $table->decimal('progress', 5, 2)->default(0);
            $table->string('status')->default('draft'); // draft, in_progress, completed
            $table->date('due_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('goal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_key_results');
    }
};
