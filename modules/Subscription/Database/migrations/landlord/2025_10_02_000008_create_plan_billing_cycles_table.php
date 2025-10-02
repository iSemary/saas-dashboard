<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_billing_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->string('country_code', 2)->nullable();
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially', 'lifetime', 'custom']);
            $table->integer('billing_interval')->default(1)->comment('Multiplier for billing cycle');
            $table->string('custom_period')->nullable()->comment('For custom billing cycles (e.g., "14 days")');
            $table->decimal('price', 10, 2);
            $table->decimal('setup_fee', 10, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0)->comment('Discount compared to monthly pricing');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['plan_id', 'currency_id', 'country_code', 'billing_cycle'], 'plan_billing_cycle_unique');
            $table->index(['plan_id', 'status', 'sort_order']);
            $table->index(['billing_cycle', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_billing_cycles');
    }
};
