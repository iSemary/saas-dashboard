<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained('plan_subscriptions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('restrict');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->string('country_code', 2)->nullable();
            $table->enum('invoice_type', ['subscription', 'setup', 'upgrade', 'downgrade', 'addon', 'credit', 'refund'])->default('subscription');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->json('line_items')->comment('Detailed breakdown of charges');
            $table->json('applied_discounts')->nullable();
            $table->json('tax_breakdown')->nullable();
            $table->datetime('invoice_date');
            $table->datetime('due_date');
            $table->datetime('period_start')->nullable();
            $table->datetime('period_end')->nullable();
            $table->datetime('paid_at')->nullable();
            $table->datetime('voided_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('external_invoice_id')->nullable()->comment('External billing system invoice ID');
            $table->json('billing_address')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ['draft', 'pending', 'paid', 'overdue', 'void', 'refunded'])->default('pending');
            $table->timestamps();

            $table->index(['brand_id', 'status']);
            $table->index(['subscription_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['invoice_date', 'due_date']);
            $table->index(['status', 'due_date']);
            $table->index('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
    }
};
