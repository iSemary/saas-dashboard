<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_prices_by_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->string('country_code', 2)->nullable()->comment('ISO 2-letter country code, null for global pricing');
            $table->integer('min_users')->default(1);
            $table->integer('max_users')->nullable()->comment('null for unlimited');
            $table->decimal('price_per_user', 10, 2);
            $table->decimal('base_price', 10, 2)->default(0)->comment('Fixed base price before per-user calculation');
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially'])->default('monthly');
            $table->enum('pricing_model', ['per_user', 'tiered', 'volume'])->default('per_user');
            $table->json('tier_discounts')->nullable()->comment('JSON array of discount percentages for volume tiers');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ['active', 'inactive', 'scheduled'])->default('active');
            $table->timestamps();

            $table->index(['plan_id', 'min_users', 'max_users']);
            $table->index(['currency_id', 'country_code']);
            $table->index(['billing_cycle', 'status']);
            $table->index(['valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_prices_by_users');
    }
};
