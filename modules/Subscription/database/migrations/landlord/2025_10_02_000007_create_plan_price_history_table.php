<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->string('country_code', 2)->nullable();
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially', 'lifetime']);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->decimal('new_price', 10, 2);
            $table->decimal('old_setup_fee', 10, 2)->nullable();
            $table->decimal('new_setup_fee', 10, 2)->nullable();
            $table->datetime('change_date');
            $table->datetime('effective_date')->nullable()->comment('When the price change takes effect');
            $table->enum('change_type', ['price_increase', 'price_decrease', 'new_pricing', 'pricing_removal'])->default('price_increase');
            $table->text('change_reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('metadata')->nullable();
            $table->enum('status', ['scheduled', 'active', 'reverted'])->default('active');
            $table->timestamps();

            $table->index(['plan_id', 'change_date']);
            $table->index(['currency_id', 'country_code']);
            $table->index(['change_date', 'effective_date']);
            $table->index(['change_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_price_history');
    }
};
