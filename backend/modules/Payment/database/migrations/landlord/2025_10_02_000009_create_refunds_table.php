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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->string('refund_id')->unique()->comment('Internal refund identifier');
            $table->foreignId('original_transaction_id')->constrained('payment_transactions')->onDelete('restrict');
            $table->foreignId('refund_transaction_id')->nullable()->constrained('payment_transactions')->onDelete('set null');
            $table->decimal('amount', 15, 2)->comment('Refund amount');
            $table->decimal('fee_refunded', 10, 2)->default(0)->comment('Fees refunded to merchant');
            $table->enum('refund_type', ['full', 'partial'])->default('full');
            $table->enum('reason', ['requested_by_customer', 'duplicate', 'fraudulent', 'subscription_cancellation', 'other'])->default('requested_by_customer');
            $table->text('reason_details')->nullable()->comment('Detailed reason for refund');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('gateway_refund_id')->nullable()->comment('Gateway-specific refund ID');
            $table->json('gateway_response')->nullable()->comment('Gateway response data');
            $table->string('initiated_by')->nullable()->comment('User who initiated the refund');
            $table->timestamp('processed_at')->nullable();
            $table->json('metadata')->nullable()->comment('Additional refund metadata');
            $table->timestamps();
            
            $table->index(['original_transaction_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index(['refund_type', 'status']);
            $table->index(['gateway_refund_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
