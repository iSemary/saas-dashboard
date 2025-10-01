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
        Schema::create('reorder_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->integer('min_quantity'); // Minimum stock level
            $table->integer('max_quantity'); // Maximum stock level
            $table->integer('reorder_quantity'); // Quantity to reorder
            $table->enum('rule_type', ['manual', 'automatic'])->default('manual');
            $table->boolean('is_active')->default(true);
            $table->integer('lead_time_days')->default(0); // Supplier lead time
            $table->decimal('safety_stock', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['product_id', 'warehouse_id']);
            $table->index(['is_active', 'rule_type']);
            $table->unique(['product_id', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reorder_rules');
    }
};
