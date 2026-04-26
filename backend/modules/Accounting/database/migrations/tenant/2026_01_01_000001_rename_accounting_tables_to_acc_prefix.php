<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chart_of_accounts') && ! Schema::hasTable('acc_chart_of_accounts')) {
            Schema::rename('chart_of_accounts', 'acc_chart_of_accounts');
        }
        if (Schema::hasTable('journal_entries') && ! Schema::hasTable('acc_journal_entries')) {
            Schema::rename('journal_entries', 'acc_journal_entries');
        }
        if (Schema::hasTable('journal_items') && ! Schema::hasTable('acc_journal_items')) {
            Schema::rename('journal_items', 'acc_journal_items');
        }
        if (Schema::hasTable('fiscal_years') && ! Schema::hasTable('acc_fiscal_years')) {
            Schema::rename('fiscal_years', 'acc_fiscal_years');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('acc_chart_of_accounts') && ! Schema::hasTable('chart_of_accounts')) {
            Schema::rename('acc_chart_of_accounts', 'chart_of_accounts');
        }
        if (Schema::hasTable('acc_journal_entries') && ! Schema::hasTable('journal_entries')) {
            Schema::rename('acc_journal_entries', 'journal_entries');
        }
        if (Schema::hasTable('acc_journal_items') && ! Schema::hasTable('journal_items')) {
            Schema::rename('acc_journal_items', 'journal_items');
        }
        if (Schema::hasTable('acc_fiscal_years') && ! Schema::hasTable('fiscal_years')) {
            Schema::rename('acc_fiscal_years', 'fiscal_years');
        }
    }
};
