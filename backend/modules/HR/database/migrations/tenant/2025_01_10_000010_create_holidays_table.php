<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->string('country')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->boolean('applies_to_all_departments')->default(true);
            $table->json('department_ids')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['date', 'country']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
