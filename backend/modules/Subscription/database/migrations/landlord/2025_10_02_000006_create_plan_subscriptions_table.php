<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('subscription_id')->unique()->comment('External subscription ID');
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('restrict');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->string('country_code', 2)->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('setup_fee', 10, 2)->default(0);
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially', 'lifetime'])->default('monthly');
            $table->integer('billing_interval')->default(1);
            $table->integer('user_count')->default(1)->comment('Number of users for usage-based pricing');
            $table->datetime('trial_starts_at')->nullable();
            $table->datetime('trial_ends_at')->nullable();
            $table->datetime('starts_at');
            $table->datetime('ends_at')->nullable();
            $table->datetime('next_billing_at')->nullable();
            $table->datetime('canceled_at')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->text('cancellation_feedback')->nullable();
            $table->json('applied_discounts')->nullable()->comment('Snapshot of applied discounts');
            $table->json('subscription_data')->nullable()->comment('Additional subscription metadata');
            $table->enum('status', ['trial', 'active', 'past_due', 'canceled', 'expired', 'suspended'])->default('trial');
            $table->enum('auto_renew', ['enabled', 'disabled', 'pending_cancellation'])->default('enabled');
            $table->timestamps();

            $table->index(['brand_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['plan_id', 'status']);
            $table->index(['status', 'next_billing_at']);
            $table->index(['trial_ends_at', 'status']);
            $table->index(['expires_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_subscriptions');
    }
};
