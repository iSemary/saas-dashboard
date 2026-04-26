<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acc_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('fiscal_year_id');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['fiscal_year_id', 'status']);
            $table->index('department_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_budgets');
    }
};
