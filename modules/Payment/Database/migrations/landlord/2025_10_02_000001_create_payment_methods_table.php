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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Display name of the payment method');
            $table->text('description')->nullable()->comment('Description of the payment method');
            $table->enum('processor_type', ['stripe', 'paypal', 'razorpay', 'adyen', 'square', 'mollie', 'braintree', 'authorize_net', 'worldpay', 'custom'])
                  ->comment('Type of payment processor');
            $table->string('gateway_name')->comment('Internal gateway identifier');
            $table->json('country_codes')->nullable()->comment('Array of supported country codes');
            $table->json('supported_currencies')->nullable()->comment('Array of supported currency codes');
            $table->boolean('is_global')->default(false)->comment('Whether this method is available globally');
            $table->json('region_restrictions')->nullable()->comment('Geographic restrictions configuration');
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->enum('authentication_type', ['api_key', 'oauth', 'certificate', 'webhook_secret'])->default('api_key');
            $table->integer('priority')->default(0)->comment('Priority for payment method selection');
            $table->decimal('success_rate', 5, 2)->default(0)->comment('Historical success rate percentage');
            $table->integer('average_processing_time')->default(0)->comment('Average processing time in milliseconds');
            $table->json('features')->nullable()->comment('Supported features (recurring, refunds, etc.)');
            $table->json('metadata')->nullable()->comment('Additional configuration metadata');
            $table->timestamps();
            
            $table->index(['status', 'is_global']);
            $table->index(['processor_type', 'status']);
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
