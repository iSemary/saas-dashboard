<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pos_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('amount', 10, 2)->default(0);
            $table->foreignId('amount_type')->nullable()->constrained('pos_types')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('image')->default('default.png');
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->foreignId('supplier_id')->nullable()->constrained('inventory_suppliers')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('pos_categories')->nullOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained('pos_sub_categories')->nullOnDelete();
            $table->integer('ordered_count')->default(0);
            $table->boolean('is_offer')->default(false);
            $table->decimal('offer_price', 12, 2)->nullable();
            $table->decimal('offer_percentage', 5, 2)->nullable();
            $table->unsignedTinyInteger('type')->default(1); // 1=product, 2=wholesale
            $table->date('production_at')->nullable();
            $table->date('expired_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['category_id', 'sub_category_id']);
            $table->index('is_offer');
            $table->index('expired_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_products');
    }
};
