<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->string('country_code', 2)->nullable()->comment('ISO 2-letter country code, null for global pricing');
            $table->decimal('price', 10, 2);
            $table->decimal('setup_fee', 10, 2)->default(0);
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially', 'lifetime'])->default('monthly');
            $table->integer('billing_interval')->default(1)->comment('Multiplier for billing cycle');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ['active', 'inactive', 'scheduled'])->default('active');
            $table->timestamps();

            $table->unique(['plan_id', 'currency_id', 'country_code', 'billing_cycle'], 'plan_price_unique');
            $table->index(['plan_id', 'status']);
            $table->index(['currency_id', 'country_code']);
            $table->index(['valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_prices');
    }
};
