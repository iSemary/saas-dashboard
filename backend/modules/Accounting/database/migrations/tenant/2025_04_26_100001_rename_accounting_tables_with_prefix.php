<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('chart_of_accounts', 'acc_chart_of_accounts');
        Schema::rename('journal_entries', 'acc_journal_entries');
        Schema::rename('journal_items', 'acc_journal_items');
        Schema::rename('fiscal_years', 'acc_fiscal_years');
    }

    public function down(): void
    {
        Schema::rename('acc_chart_of_accounts', 'chart_of_accounts');
        Schema::rename('acc_journal_entries', 'journal_entries');
        Schema::rename('acc_journal_items', 'journal_items');
        Schema::rename('acc_fiscal_years', 'fiscal_years');
    }
};
