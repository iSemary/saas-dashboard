<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('type'); // earning, deduction
            $table->string('category'); // basic, overtime, bonus, tax, social_security, etc.
            $table->string('description');
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('quantity', 8, 2)->nullable();
            $table->string('unit')->nullable(); // hours, days, percentage
            $table->decimal('rate', 12, 2)->nullable();
            $table->boolean('is_taxable')->default(true);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['payroll_id', 'type']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
