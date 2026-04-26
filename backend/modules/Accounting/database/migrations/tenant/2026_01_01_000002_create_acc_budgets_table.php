<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acc_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('fiscal_year_id');
            $table->string('department')->nullable();
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('spent_amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->unsignedBigInteger('created_by');
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['fiscal_year_id', 'status']);
            $table->index('department');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_budgets');
    }
};
