<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_client_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('sales_clients')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('sales_orders')->onDelete('cascade');
            $table->unique(['client_id', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_client_orders');
    }
};
