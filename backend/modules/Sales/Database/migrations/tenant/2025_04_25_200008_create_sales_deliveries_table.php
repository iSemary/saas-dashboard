<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('full_name');
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();
            $table->string('delivery_man')->nullable();
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('sales_orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_deliveries');
    }
};
