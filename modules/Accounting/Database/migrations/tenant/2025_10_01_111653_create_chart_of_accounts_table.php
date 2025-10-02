<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['asset', 'liability', 'equity', 'income', 'expense']);
            $table->enum('sub_type', [
                'current_asset', 'fixed_asset', 'intangible_asset',
                'current_liability', 'long_term_liability',
                'equity', 'retained_earnings',
                'operating_income', 'non_operating_income',
                'operating_expense', 'non_operating_expense'
            ])->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('level')->default(1);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_leaf')->default(true);
            $table->boolean('reconcile')->default(false);
            $table->string('currency', 3)->default('USD');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by');
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index(['parent_id', 'level']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
