<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('max_amount', 12, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_expense_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('hr_expense_categories')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->date('expense_date')->nullable();
            $table->text('description')->nullable();
            $table->string('receipt_path')->nullable();
            $table->string('status')->default('submitted');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_expense_claims');
        Schema::dropIfExists('hr_expense_categories');
    }
};
