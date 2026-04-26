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
        Schema::table('brand_modules', function (Blueprint $table) {
            // Pricing & billing fields for add-on modules
            $table->decimal('price', 12, 2)->nullable()->after('module_config');
            $table->foreignId('currency_id')->nullable()->after('price')->constrained('currencies')->onDelete('set null');
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially', 'lifetime'])->default('monthly')->after('currency_id');
            
            // Subscription lifecycle
            $table->datetime('subscription_start')->nullable()->after('billing_cycle');
            $table->datetime('current_period_start')->nullable()->after('subscription_start');
            $table->datetime('current_period_end')->nullable()->after('current_period_start');
            $table->datetime('next_billing_at')->nullable()->after('current_period_end');
            $table->datetime('canceled_at')->nullable()->after('next_billing_at');
            $table->boolean('cancel_at_period_end')->default(false)->after('canceled_at');
            
            // Gateway tracking
            $table->string('gateway')->nullable()->after('cancel_at_period_end')->comment('stripe, paypal, mock');
            $table->string('gateway_subscription_id')->nullable()->after('gateway')->comment('Stripe subscription ID or PayPal agreement ID');
            $table->string('gateway_customer_id')->nullable()->after('gateway_subscription_id');
            
            // Trial
            $table->datetime('trial_starts_at')->nullable()->after('gateway_customer_id');
            $table->datetime('trial_ends_at')->nullable()->after('trial_starts_at');
            
            // Indexes
            $table->index(['brand_id', 'subscription_status', 'next_billing_at']);
            $table->index(['gateway_subscription_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_modules', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropIndex(['brand_id', 'subscription_status', 'next_billing_at']);
            $table->dropIndex(['gateway_subscription_id']);
            $table->dropColumn([
                'price',
                'currency_id',
                'billing_cycle',
                'subscription_start',
                'current_period_start',
                'current_period_end',
                'next_billing_at',
                'canceled_at',
                'cancel_at_period_end',
                'gateway',
                'gateway_subscription_id',
                'gateway_customer_id',
                'trial_starts_at',
                'trial_ends_at',
            ]);
        });
    }
};
