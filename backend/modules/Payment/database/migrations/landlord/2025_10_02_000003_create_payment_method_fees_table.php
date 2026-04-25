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
        if (!Schema::hasTable('payment_method_fees')) {
            Schema::create('payment_method_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->enum('fee_type', ['percentage', 'fixed', 'tiered', 'mixed'])->default('percentage');
            $table->decimal('fee_value', 8, 4)->default(0)->comment('Primary fee value (percentage or fixed amount)');
            $table->decimal('min_fee', 10, 2)->nullable()->comment('Minimum fee amount');
            $table->decimal('max_fee', 10, 2)->nullable()->comment('Maximum fee amount');
            $table->json('fee_tiers')->nullable()->comment('Tiered pricing structure');
            $table->decimal('fixed_fee', 10, 2)->default(0)->comment('Fixed fee component for mixed type');
            $table->enum('applies_to', ['sale', 'refund', 'chargeback', 'subscription', 'all'])->default('sale');
            $table->string('region')->nullable()->comment('Geographic region this fee applies to');
            $table->enum('customer_segment', ['all', 'new', 'existing', 'vip', 'enterprise'])->default('all');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->date('effective_from')->nullable()->comment('When this fee structure becomes effective');
            $table->date('effective_until')->nullable()->comment('When this fee structure expires');
            $table->timestamps();

            $table->index(['payment_method_id', 'currency_id', 'applies_to'], 'pm_fee_idx');
            $table->index(['status', 'effective_from', 'effective_until'], 'pm_fee_status_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_method_fees');
    }
};
