<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acc_bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_account_id');
            $table->date('date');
            $table->enum('type', ['debit', 'credit']);
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('reference')->nullable();
            $table->boolean('is_reconciled')->default(false);
            $table->unsignedBigInteger('reconciliation_id')->nullable();
            $table->unsignedBigInteger('journal_item_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['bank_account_id', 'is_reconciled']);
            $table->index(['date', 'type']);
            $table->foreign('bank_account_id')->references('id')->on('acc_bank_accounts')->cascadeOnDelete();
            $table->foreign('reconciliation_id')->references('id')->on('acc_reconciliations')->nullOnDelete();
            $table->foreign('journal_item_id')->references('id')->on('acc_journal_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_bank_transactions');
    }
};
