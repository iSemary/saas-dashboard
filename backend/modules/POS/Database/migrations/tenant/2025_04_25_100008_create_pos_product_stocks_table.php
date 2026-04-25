<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pos_product_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('pos_products')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->json('tag_id')->nullable();
            $table->integer('quantity');
            $table->unsignedBigInteger('object_id')->nullable();
            $table->string('model'); // order, returned, damaged, offer_price, purchases
            $table->decimal('main_price', 12, 2)->nullable();
            $table->decimal('total_price', 12, 2)->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->string('barcode')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['product_id', 'branch_id']);
            $table->index(['model', 'object_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_product_stocks');
    }
};
