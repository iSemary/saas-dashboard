<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->json('products');
            $table->decimal('total_price', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('tax', 8, 2)->default(0);
            $table->string('barcode')->nullable()->unique();
            $table->string('pay_method')->default('cash');
            $table->string('transaction_number')->nullable();
            $table->string('status')->default('completed');
            $table->string('order_type')->default('takeaway');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('branch_id');
            $table->index('pay_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
