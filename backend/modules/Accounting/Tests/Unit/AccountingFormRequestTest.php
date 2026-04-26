<?php

namespace Modules\Accounting\Tests\Unit;

use Modules\Accounting\Presentation\Http\Requests\StoreChartOfAccountRequest;
use Modules\Accounting\Presentation\Http\Requests\StoreJournalEntryRequest;
use Modules\Accounting\Presentation\Http\Requests\StoreFiscalYearRequest;
use Modules\Accounting\Presentation\Http\Requests\StoreBudgetRequest;
use Modules\Accounting\Presentation\Http\Requests\StoreTaxRateRequest;
use Modules\Accounting\Presentation\Http\Requests\StoreBankAccountRequest;
use Modules\Accounting\Presentation\Http\Requests\StoreReconciliationRequest;
use PHPUnit\Framework\TestCase;

class AccountingFormRequestTest extends TestCase
{
    public function test_store_chart_of_account_has_required_rules(): void
    {
        $request = new StoreChartOfAccountRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('code', $rules);
        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('type', $rules);
        $this->assertStringContainsString('required', $rules['code']);
        $this->assertStringContainsString('required', $rules['name']);
        $this->assertStringContainsString('required', $rules['type']);
    }

    public function test_store_chart_of_account_type_is_enum_validated(): void
    {
        $request = new StoreChartOfAccountRequest();
        $rules = $request->rules();

        $this->assertStringContainsString('in:asset,liability,equity,income,expense', $rules['type']);
    }

    public function test_store_chart_of_account_parent_id_exists_validation(): void
    {
        $request = new StoreChartOfAccountRequest();
        $rules = $request->rules();

        $this->assertStringContainsString('exists:acc_chart_of_accounts,id', $rules['parent_id']);
    }

    public function test_store_journal_entry_has_required_rules(): void
    {
        $request = new StoreJournalEntryRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('entry_date', $rules);
        $this->assertArrayHasKey('fiscal_year_id', $rules);
        $this->assertStringContainsString('required', $rules['entry_date']);
        $this->assertStringContainsString('required', $rules['fiscal_year_id']);
    }

    public function test_store_journal_entry_items_are_validated(): void
    {
        $request = new StoreJournalEntryRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('items', $rules);
        $this->assertArrayHasKey('items.*.account_id', $rules);
        $this->assertStringContainsString('exists:acc_chart_of_accounts,id', $rules['items.*.account_id']);
    }

    public function test_store_fiscal_year_has_required_date_fields(): void
    {
        $request = new StoreFiscalYearRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('start_date', $rules);
        $this->assertArrayHasKey('end_date', $rules);
        $this->assertStringContainsString('required', $rules['start_date']);
        $this->assertStringContainsString('required', $rules['end_date']);
    }

    public function test_store_budget_has_required_rules(): void
    {
        $request = new StoreBudgetRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('fiscal_year_id', $rules);
        $this->assertStringContainsString('required', $rules['fiscal_year_id']);
    }

    public function test_store_tax_rate_has_required_rules(): void
    {
        $request = new StoreTaxRateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('rate', $rules);
        $this->assertStringContainsString('required', $rules['name']);
        $this->assertStringContainsString('required', $rules['rate']);
    }

    public function test_store_bank_account_has_required_rules(): void
    {
        $request = new StoreBankAccountRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('bank_name', $rules);
        $this->assertArrayHasKey('account_number', $rules);
        $this->assertStringContainsString('required', $rules['bank_name']);
        $this->assertStringContainsString('required', $rules['account_number']);
    }

    public function test_store_reconciliation_has_required_rules(): void
    {
        $request = new StoreReconciliationRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('bank_account_id', $rules);
        $this->assertStringContainsString('required', $rules['bank_account_id']);
    }
}
