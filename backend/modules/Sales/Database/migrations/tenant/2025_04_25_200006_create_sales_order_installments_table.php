<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_order_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('installment_type')->nullable();
            $table->unsignedInteger('total_months')->default(1);
            $table->unsignedInteger('paid_months')->default(0);
            $table->decimal('monthly_amount', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('sales_orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_installments');
    }
};
