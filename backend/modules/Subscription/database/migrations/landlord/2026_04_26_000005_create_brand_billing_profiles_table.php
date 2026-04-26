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
        Schema::create('brand_billing_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->unique()->constrained('brands')->onDelete('cascade');
            
            // Default gateway
            $table->enum('default_gateway', ['stripe', 'paypal', 'mock'])->default('stripe');
            
            // Gateway customer IDs
            $table->string('stripe_customer_id')->nullable();
            $table->string('paypal_payer_id')->nullable();
            
            // Default payment method
            $table->foreignId('default_payment_method_id')->nullable()->constrained('customer_payment_methods')->onDelete('set null');
            
            // Billing info
            $table->string('tax_id', 50)->nullable()->comment('VAT/GST/Tax ID');
            $table->string('tax_id_type', 20)->nullable()->comment('vat, gst, ein, etc.');
            $table->json('billing_address')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('billing_phone', 30)->nullable();
            
            // Balance & credit
            $table->decimal('account_balance', 12, 2)->default(0)->comment('Credit (positive) or debt (negative)');
            $table->string('currency_code', 3)->default('USD');
            
            // Invoice settings
            $table->boolean('auto_pay')->default(true);
            $table->boolean('paperless_billing')->default(true);
            $table->string('invoice_email_cc')->nullable();
            
            // Status
            $table->enum('status', ['active', 'suspended', 'closed'])->default('active');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['brand_id', 'status']);
            $table->index(['stripe_customer_id']);
            $table->index(['paypal_payer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_billing_profiles');
    }
};
