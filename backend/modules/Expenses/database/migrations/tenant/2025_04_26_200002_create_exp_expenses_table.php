<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exp_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->date('date');
            $table->unsignedBigInteger('category_id');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'reimbursed', 'cancelled'])->default('draft');
            $table->string('reference')->nullable();
            $table->string('vendor')->nullable();
            $table->string('receipt')->nullable();
            $table->date('receipt_date')->nullable();
            $table->boolean('is_billable')->default(false);
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('report_id')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->dateTime('reimbursed_at')->nullable();
            $table->unsignedBigInteger('reimbursed_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['category_id', 'status']);
            $table->index(['created_by', 'status']);
            $table->index('date');
            $table->foreign('category_id')->references('id')->on('exp_categories')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exp_expenses');
    }
};
