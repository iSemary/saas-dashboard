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
        Schema::create('payment_gateway_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained('payment_transactions')->onDelete('set null');
            $table->enum('log_level', ['debug', 'info', 'warning', 'error', 'critical'])->default('info');
            $table->string('operation')->comment('Type of operation (payment, refund, webhook, etc.)');
            $table->json('request_data')->nullable()->comment('Request data sent to gateway');
            $table->json('response_data')->nullable()->comment('Response data from gateway');
            $table->string('endpoint_called')->nullable()->comment('Gateway endpoint that was called');
            $table->integer('http_status')->nullable()->comment('HTTP status code returned');
            $table->integer('processing_time_ms')->nullable()->comment('Processing time in milliseconds');
            $table->string('error_code')->nullable()->comment('Gateway-specific error code');
            $table->text('error_message')->nullable()->comment('Error message if operation failed');
            $table->string('correlation_id')->nullable()->comment('Correlation ID for tracking related operations');
            $table->json('headers')->nullable()->comment('HTTP headers from request/response');
            $table->string('gateway_request_id')->nullable()->comment('Gateway-provided request ID');
            $table->boolean('is_webhook')->default(false)->comment('Whether this log is from a webhook');
            $table->string('ip_address')->nullable()->comment('IP address of the request');
            $table->timestamps();
            
            $table->index(['payment_method_id', 'created_at']);
            $table->index(['log_level', 'created_at']);
            $table->index(['operation', 'created_at']);
            $table->index(['correlation_id']);
            $table->index(['is_webhook', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_logs');
    }
};
