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
        if (!Schema::hasTable('payment_method_limits')) {
            Schema::create('payment_method_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->enum('limit_type', ['transaction', 'daily', 'weekly', 'monthly', 'yearly', 'per_customer'])->default('transaction');
            $table->decimal('min_limit', 15, 2)->default(0)->comment('Minimum transaction/limit amount');
            $table->decimal('max_limit', 15, 2)->nullable()->comment('Maximum transaction/limit amount');
            $table->integer('limit_duration')->nullable()->comment('Duration in hours for rolling limits');
            $table->integer('transaction_count_limit')->nullable()->comment('Maximum number of transactions in period');
            $table->enum('customer_segment', ['all', 'new', 'existing', 'vip', 'enterprise'])->default('all');
            $table->string('region')->nullable()->comment('Geographic region this limit applies to');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('conditions')->nullable()->comment('Additional conditions for limit application');
            $table->timestamps();

            $table->index(['payment_method_id', 'currency_id', 'limit_type'], 'pm_limit_idx');
            $table->index(['customer_segment', 'status'], 'pm_limit_seg_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_method_limits');
    }
};
