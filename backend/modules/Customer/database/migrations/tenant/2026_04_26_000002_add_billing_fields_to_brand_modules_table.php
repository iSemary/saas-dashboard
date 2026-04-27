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
            if (!Schema::hasColumn('brand_modules', 'price')) {
                $table->decimal('price', 12, 2)->nullable()->after('module_config');
            }
            if (!Schema::hasColumn('brand_modules', 'currency_id')) {
                $table->unsignedBigInteger('currency_id')->nullable()->after('price');
            }
            if (!Schema::hasColumn('brand_modules', 'billing_cycle')) {
                $table->enum('billing_cycle', ['monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially', 'lifetime'])->default('monthly')->after('currency_id');
            }

            // Subscription lifecycle
            if (!Schema::hasColumn('brand_modules', 'subscription_status')) {
                $table->enum('subscription_status', ['active', 'trialing', 'past_due', 'canceled', 'unpaid', 'incomplete'])->default('active')->after('billing_cycle');
            }
            if (!Schema::hasColumn('brand_modules', 'subscription_start')) {
                $table->datetime('subscription_start')->nullable()->after('subscription_status');
            }
            if (!Schema::hasColumn('brand_modules', 'current_period_start')) {
                $table->datetime('current_period_start')->nullable()->after('subscription_start');
            }
            if (!Schema::hasColumn('brand_modules', 'current_period_end')) {
                $table->datetime('current_period_end')->nullable()->after('current_period_start');
            }
            if (!Schema::hasColumn('brand_modules', 'next_billing_at')) {
                $table->datetime('next_billing_at')->nullable()->after('current_period_end');
            }
            if (!Schema::hasColumn('brand_modules', 'canceled_at')) {
                $table->datetime('canceled_at')->nullable()->after('next_billing_at');
            }
            if (!Schema::hasColumn('brand_modules', 'cancel_at_period_end')) {
                $table->boolean('cancel_at_period_end')->default(false)->after('canceled_at');
            }

            // Gateway tracking
            if (!Schema::hasColumn('brand_modules', 'gateway')) {
                $table->string('gateway')->nullable()->after('cancel_at_period_end')->comment('stripe, paypal, mock');
            }
            if (!Schema::hasColumn('brand_modules', 'gateway_subscription_id')) {
                $table->string('gateway_subscription_id')->nullable()->after('gateway')->comment('Stripe subscription ID or PayPal agreement ID');
            }
            if (!Schema::hasColumn('brand_modules', 'gateway_customer_id')) {
                $table->string('gateway_customer_id')->nullable()->after('gateway_subscription_id');
            }

            // Trial
            if (!Schema::hasColumn('brand_modules', 'trial_starts_at')) {
                $table->datetime('trial_starts_at')->nullable()->after('gateway_customer_id');
            }
            if (!Schema::hasColumn('brand_modules', 'trial_ends_at')) {
                $table->datetime('trial_ends_at')->nullable()->after('trial_starts_at');
            }

            // Indexes
            if (!Schema::hasIndex('brand_modules', 'brand_modules_brand_id_subscription_status_next_billing_at_index')) {
                $table->index(['brand_id', 'subscription_status', 'next_billing_at']);
            }
            if (!Schema::hasIndex('brand_modules', 'brand_modules_gateway_subscription_id_index')) {
                $table->index(['gateway_subscription_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_modules', function (Blueprint $table) {
            $table->dropIndex(['brand_id', 'subscription_status', 'next_billing_at']);
            $table->dropIndex(['gateway_subscription_id']);
            $table->dropColumn([
                'price',
                'currency_id',
                'billing_cycle',
                'subscription_status',
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
