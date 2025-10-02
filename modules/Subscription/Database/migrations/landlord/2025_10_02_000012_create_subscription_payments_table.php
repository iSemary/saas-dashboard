<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('plan_subscriptions')->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained('subscription_invoices')->onDelete('set null');
            $table->foreignId('payment_transaction_id')->nullable()->constrained('payment_transactions')->onDelete('set null');
            $table->string('payment_id')->unique()->comment('Internal payment reference');
            $table->string('external_payment_id')->nullable()->comment('External payment gateway ID');
            $table->decimal('amount', 10, 2);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->enum('payment_type', ['subscription', 'setup', 'upgrade', 'downgrade', 'addon', 'late_fee', 'refund'])->default('subscription');
            $table->enum('payment_method_type', ['card', 'bank_transfer', 'paypal', 'stripe', 'other'])->nullable();
            $table->string('payment_method_details')->nullable()->comment('Last 4 digits, bank name, etc.');
            $table->datetime('attempted_at');
            $table->datetime('processed_at')->nullable();
            $table->datetime('failed_at')->nullable();
            $table->datetime('refunded_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->integer('retry_count')->default(0);
            $table->datetime('next_retry_at')->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'canceled', 'refunded'])->default('pending');
            $table->timestamps();

            $table->index(['subscription_id', 'status']);
            $table->index(['invoice_id', 'status']);
            $table->index(['payment_transaction_id']);
            $table->index(['status', 'attempted_at']);
            $table->index(['status', 'next_retry_at']);
            $table->index('payment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
