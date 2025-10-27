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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('quotation_id')->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->date('order_date');
            $table->date('delivery_date')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('draft');
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue', 'refunded'])->default('pending');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('shipping_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->text('shipping_address')->nullable();
            $table->text('billing_address')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'order_date']);
            $table->index(['payment_status', 'order_date']);
            $table->index(['contact_id', 'status']);
            $table->index(['company_id', 'status']);
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
