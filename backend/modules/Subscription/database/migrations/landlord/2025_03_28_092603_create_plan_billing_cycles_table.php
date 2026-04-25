<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('plan_billing_cycles')) {
            Schema::create('plan_billing_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->enum('billing_cycle', ['monthly', 'yearly', 'quarterly', 'semi-annually'])->default('monthly');
            $table->decimal('price', 12, 2);
            $table->foreignId('currency_id')->constrained('currencies');
            $table->integer('billing_interval')->default(1);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_billing_cycles');
    }
};
