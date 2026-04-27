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
        Schema::create('subscription_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('subscription_invoices')->onDelete('cascade');

            // Line type
            $table->enum('line_type', ['plan', 'module', 'discount', 'proration', 'tax', 'setup_fee', 'adjustment'])->default('plan');

            // Polymorphic reference to source (PlanSubscription or BrandModuleSubscription)
            $table->morphs('reference');

            // Description & pricing
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('amount', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('set null');

            // Period (for proration)
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();

            // Metadata
            $table->json('metadata')->nullable()->comment('Proration calculation details, discount codes, etc.');

            $table->timestamps();

            // Indexes
            $table->index(['invoice_id', 'line_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_invoice_items');
    }
};
