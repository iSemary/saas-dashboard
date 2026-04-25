<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_order_stewards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('cashier_id')->nullable();
            $table->unsignedBigInteger('steward_id')->nullable();
            $table->string('order_number')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('sales_orders')->onDelete('cascade');
            $table->index(['steward_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_stewards');
    }
};
