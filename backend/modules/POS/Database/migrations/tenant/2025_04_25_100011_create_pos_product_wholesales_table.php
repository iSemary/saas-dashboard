<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pos_product_wholesales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('pos_products')->cascadeOnDelete();
            $table->foreignId('child_id')->constrained('pos_products')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['parent_id', 'child_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_product_wholesales');
    }
};
