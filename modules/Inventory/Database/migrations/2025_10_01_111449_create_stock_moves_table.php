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
        Schema::create('stock_moves', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->enum('move_type', ['in', 'out', 'internal']); // in, out, internal transfer
            $table->enum('origin_type', ['purchase', 'sale', 'adjustment', 'production', 'transfer', 'return'])->nullable();
            $table->unsignedBigInteger('origin_id')->nullable(); // Reference to source document
            $table->integer('quantity');
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->date('date');
            $table->enum('state', ['draft', 'confirmed', 'done', 'cancel'])->default('draft');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['product_id', 'warehouse_id']);
            $table->index(['move_type', 'date']);
            $table->index(['origin_type', 'origin_id']);
            $table->index(['state', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_moves');
    }
};
