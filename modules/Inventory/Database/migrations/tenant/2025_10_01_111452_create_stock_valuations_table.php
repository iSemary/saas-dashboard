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
        Schema::create('stock_valuations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->integer('quantity');
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_cost', 15, 2);
            $table->enum('valuation_method', ['fifo', 'lifo', 'average', 'standard'])->default('fifo');
            $table->date('valuation_date');
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['product_id', 'warehouse_id']);
            $table->index(['valuation_date', 'valuation_method']);
            $table->unique(['product_id', 'warehouse_id', 'valuation_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_valuations');
    }
};
