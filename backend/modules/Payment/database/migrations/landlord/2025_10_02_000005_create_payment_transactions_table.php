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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique()->comment('Internal transaction identifier');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('restrict');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->decimal('amount', 15, 2)->comment('Transaction amount in specified currency');
            $table->decimal('base_currency_amount', 15, 2)->comment('Amount converted to base currency');
            $table->decimal('exchange_rate_used', 10, 6)->comment('Exchange rate used for conversion');
            $table->string('customer_id')->nullable()->comment('Customer identifier');
            $table->string('merchant_account_id')->nullable()->comment('Merchant account used for processing');
            $table->string('gateway_transaction_id')->nullable()->comment('Gateway-specific transaction ID');
            $table->string('gateway_reference')->nullable()->comment('Additional gateway reference');
            $table->enum('transaction_type', ['sale', 'refund', 'auth', 'capture', 'void', 'subscription', 'recurring'])->default('sale');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded', 'partially_refunded', 'charged_back'])->default('pending');
            $table->json('gateway_response')->nullable()->comment('Full gateway response data');
            $table->json('error_details')->nullable()->comment('Error information if transaction failed');
            $table->json('metadata')->nullable()->comment('Additional transaction metadata');
            $table->enum('settlement_status', ['pending', 'processing', 'settled', 'failed'])->default('pending');
            $table->timestamp('settlement_date')->nullable();
            $table->json('fees_breakdown')->nullable()->comment('Detailed fee breakdown');
            $table->decimal('total_fees', 10, 2)->default(0)->comment('Total fees charged');
            $table->decimal('net_amount', 15, 2)->comment('Amount after fees');
            $table->string('invoice_number')->nullable()->comment('Associated invoice number');
            $table->string('order_id')->nullable()->comment('Associated order identifier');
            $table->text('description')->nullable()->comment('Transaction description');
            $table->string('customer_ip')->nullable()->comment('Customer IP address');
            $table->string('user_agent')->nullable()->comment('Customer user agent');
            $table->json('billing_address')->nullable()->comment('Billing address information');
            $table->json('shipping_address')->nullable()->comment('Shipping address information');
            $table->string('payment_method_details')->nullable()->comment('Payment method details (last 4 digits, etc.)');
            $table->boolean('is_test')->default(false)->comment('Whether this is a test transaction');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index(['customer_id', 'status']);
            $table->index(['gateway_transaction_id']);
            $table->index(['transaction_type', 'status']);
            $table->index(['settlement_status', 'settlement_date']);
            $table->index(['is_test', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
