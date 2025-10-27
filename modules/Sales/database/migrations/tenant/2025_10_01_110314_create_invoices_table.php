<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->enum('status', ['draft', 'sent', 'viewed', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance_amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'invoice_date']);
            $table->index(['due_date', 'status']);
            $table->index(['contact_id', 'status']);
            $table->index(['company_id', 'status']);
            $table->index('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
