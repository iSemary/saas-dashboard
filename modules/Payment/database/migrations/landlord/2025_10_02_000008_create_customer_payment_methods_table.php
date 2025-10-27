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
        Schema::create('customer_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->comment('Customer identifier');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->string('gateway_token')->nullable()->comment('Gateway-specific customer/payment method token');
            $table->string('gateway_customer_id')->nullable()->comment('Gateway-specific customer ID');
            $table->text('method_details')->nullable()->comment('Encrypted payment method details');
            $table->string('payment_type')->nullable()->comment('Type of payment method (card, bank, wallet, etc.)');
            $table->string('last_four')->nullable()->comment('Last 4 digits for display purposes');
            $table->string('brand')->nullable()->comment('Card brand or payment method brand');
            $table->string('expiry_month')->nullable()->comment('Expiry month for cards');
            $table->string('expiry_year')->nullable()->comment('Expiry year for cards');
            $table->string('holder_name')->nullable()->comment('Cardholder or account holder name');
            $table->json('billing_address')->nullable()->comment('Billing address associated with payment method');
            $table->boolean('is_default')->default(false)->comment('Whether this is the default payment method');
            $table->boolean('is_verified')->default(false)->comment('Whether the payment method is verified');
            $table->enum('status', ['active', 'inactive', 'expired', 'failed_verification'])->default('active');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->json('metadata')->nullable()->comment('Additional metadata');
            $table->timestamps();
            
            $table->index(['customer_id', 'status']);
            $table->index(['payment_method_id', 'status']);
            $table->index(['gateway_token']);
            $table->index(['is_default', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_payment_methods');
    }
};
