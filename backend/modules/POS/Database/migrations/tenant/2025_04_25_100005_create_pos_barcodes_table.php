<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pos_barcodes', function (Blueprint $table) {
            $table->id();
            $table->string('barcode_number');
            $table->foreignId('product_id')->constrained('pos_products')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('pos_categories')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('barcode_number');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_barcodes');
    }
};
