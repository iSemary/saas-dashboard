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
        Schema::create('payment_method_currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->foreignId('processing_currency_id')->nullable()->constrained('currencies')->onDelete('set null')
                  ->comment('Currency used for processing (may differ from display currency)');
            $table->integer('settlement_days')->default(0)->comment('Days until settlement');
            $table->enum('settlement_schedule', ['instant', 'daily', 'weekly', 'monthly', 'custom'])->default('daily');
            $table->decimal('conversion_rate', 10, 6)->nullable()->comment('Custom conversion rate if different from base rate');
            $table->boolean('auto_conversion')->default(true)->comment('Whether to automatically convert currencies');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            $table->unique(['payment_method_id', 'currency_id']);
            $table->index(['currency_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_method_currencies');
    }
};
