<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique()->nullable()->comment('Discount/coupon code');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed', 'free_trial', 'free_months'])->default('percentage');
            $table->decimal('discount_value', 10, 2);
            $table->enum('applies_to', ['first_payment', 'recurring', 'lifetime', 'specific_cycles'])->default('first_payment');
            $table->integer('cycle_count')->nullable()->comment('Number of cycles for specific_cycles type');
            $table->integer('usage_limit')->nullable()->comment('Total usage limit across all customers');
            $table->integer('usage_limit_per_customer')->default(1);
            $table->integer('usage_count')->default(0);
            $table->decimal('minimum_amount', 10, 2)->nullable()->comment('Minimum order amount to apply discount');
            $table->json('applicable_countries')->nullable()->comment('Country codes where discount applies');
            $table->json('applicable_currencies')->nullable()->comment('Currency codes where discount applies');
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->boolean('is_stackable')->default(false)->comment('Can be combined with other discounts');
            $table->json('metadata')->nullable();
            $table->enum('status', ['active', 'inactive', 'expired', 'exhausted'])->default('active');
            $table->timestamps();

            $table->index(['plan_id', 'status']);
            $table->index(['code', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index('usage_count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_discounts');
    }
};
