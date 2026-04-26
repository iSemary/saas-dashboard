<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acc_budget_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budget_id');
            $table->unsignedBigInteger('account_id');
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['budget_id', 'account_id']);
            $table->foreign('budget_id')->references('id')->on('acc_budgets')->cascadeOnDelete();
            $table->foreign('account_id')->references('id')->on('acc_chart_of_accounts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_budget_items');
    }
};
