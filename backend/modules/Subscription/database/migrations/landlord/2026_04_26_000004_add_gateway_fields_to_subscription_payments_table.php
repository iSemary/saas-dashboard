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
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->enum('gateway', ['stripe', 'paypal', 'mock'])->nullable()->after('currency_id');
            $table->string('gateway_payment_id')->nullable()->after('gateway')->comment('Stripe PaymentIntent ID, PayPal transaction ID');
            $table->string('gateway_customer_id')->nullable()->after('gateway_payment_id');
            $table->json('gateway_payload')->nullable()->after('gateway_customer_id')->comment('Raw gateway response');
            $table->string('failure_code', 50)->nullable()->after('gateway_payload');
            $table->text('failure_message')->nullable()->after('failure_code');
            $table->datetime('paid_at')->nullable()->after('failure_message');
            $table->decimal('refund_amount', 12, 2)->nullable()->after('paid_at');

            // Index for gateway lookups
            $table->index(['gateway', 'gateway_payment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->dropIndex(['gateway', 'gateway_payment_id']);
            $table->dropColumn([
                'gateway',
                'gateway_payment_id',
                'gateway_customer_id',
                'gateway_payload',
                'failure_code',
                'failure_message',
                'paid_at',
                'refund_amount',
            ]);
        });
    }
};
