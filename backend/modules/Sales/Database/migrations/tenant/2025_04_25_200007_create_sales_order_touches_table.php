<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_order_touches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('order_type')->nullable();
            $table->string('table_number')->nullable();
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('sales_orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_touches');
    }
};
