<?php

namespace Modules\Accounting\Tests\Unit\DTOs;

use PHPUnit\Framework\TestCase;
use Modules\Accounting\Application\DTOs\ChartOfAccount\CreateChartOfAccountDTO;
use Modules\Accounting\Application\DTOs\ChartOfAccount\UpdateChartOfAccountDTO;
use Modules\Accounting\Application\DTOs\JournalEntry\CreateJournalEntryDTO;
use Modules\Accounting\Application\DTOs\JournalEntry\UpdateJournalEntryDTO;
use Modules\Accounting\Application\DTOs\FiscalYear\CreateFiscalYearDTO;
use Modules\Accounting\Application\DTOs\Budget\CreateBudgetDTO;
use Modules\Accounting\Application\DTOs\TaxRate\CreateTaxRateDTO;
use Modules\Accounting\Application\DTOs\BankAccount\CreateBankAccountDTO;
use Modules\Accounting\Application\DTOs\Reconciliation\CreateReconciliationDTO;

class AccountingDtoTest extends TestCase
{
    // ── CreateChartOfAccountDTO ──────────────────────────────────

    public function test_create_chart_of_account_dto_instantiation(): void
    {
        $dto = new CreateChartOfAccountDTO(
            code: '1000',
            name: 'Cash',
            type: 'asset',
            sub_type: 'current_asset',
        );

        $this->assertSame('1000', $dto->code);
        $this->assertSame('Cash', $dto->name);
        $this->assertSame('asset', $dto->type);
        $this->assertSame('current_asset', $dto->sub_type);
    }

    public function test_create_chart_of_account_dto_to_array_filters_nulls(): void
    {
        $dto = new CreateChartOfAccountDTO(
            code: '2000',
            name: 'Accounts Payable',
            type: 'liability',
        );

        $array = $dto->toArray();
        $this->assertArrayHasKey('code', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayNotHasKey('description', $array);
        $this->assertArrayNotHasKey('parent_id', $array);
        $this->assertArrayNotHasKey('custom_fields', $array);
    }

    // ── UpdateChartOfAccountDTO ──────────────────────────────────

    public function test_update_chart_of_account_dto_filters_nulls(): void
    {
        $dto = new UpdateChartOfAccountDTO(
            name: 'Updated Name',
            is_active: false,
        );

        $array = $dto->toArray();
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('is_active', $array);
        $this->assertArrayNotHasKey('code', $array);
        $this->assertArrayNotHasKey('type', $array);
    }

    // ── CreateJournalEntryDTO ─────────────────────────────────────

    public function test_create_journal_entry_dto_instantiation(): void
    {
        $dto = new CreateJournalEntryDTO(
            entry_number: 'JE-001',
            entry_date: '2025-06-15',
            description: 'Monthly rent',
            reference: 'REF-001',
        );

        $this->assertSame('JE-001', $dto->entry_number);
        $this->assertSame('2025-06-15', $dto->entry_date);
        $this->assertSame('Monthly rent', $dto->description);
    }

    // ── UpdateJournalEntryDTO ─────────────────────────────────────

    public function test_update_journal_entry_dto_filters_nulls(): void
    {
        $dto = new UpdateJournalEntryDTO(description: 'Updated desc');
        $array = $dto->toArray();

        $this->assertArrayHasKey('description', $array);
        $this->assertArrayNotHasKey('entry_date', $array);
    }

    // ── CreateFiscalYearDTO ───────────────────────────────────────

    public function test_create_fiscal_year_dto_instantiation(): void
    {
        $dto = new CreateFiscalYearDTO(
            name: 'FY 2025',
            start_date: '2025-01-01',
            end_date: '2025-12-31',
        );

        $this->assertSame('FY 2025', $dto->name);
        $this->assertSame('2025-01-01', $dto->start_date);
    }

    // ── CreateBudgetDTO ───────────────────────────────────────────

    public function test_create_budget_dto_instantiation(): void
    {
        $dto = new CreateBudgetDTO(
            name: 'Q1 Budget',
            fiscal_year_id: 1,
        );

        $this->assertSame('Q1 Budget', $dto->name);
        $this->assertSame(1, $dto->fiscal_year_id);
        $this->assertSame('draft', $dto->status);
    }

    // ── CreateTaxRateDTO ──────────────────────────────────────────

    public function test_create_tax_rate_dto_instantiation(): void
    {
        $dto = new CreateTaxRateDTO(
            name: 'VAT',
            code: 'VAT20',
            rate: 20.0,
        );

        $this->assertSame('VAT', $dto->name);
        $this->assertSame('VAT20', $dto->code);
        $this->assertSame(20.0, $dto->rate);
        $this->assertTrue($dto->is_active);
        $this->assertFalse($dto->is_compound);
    }

    // ── CreateBankAccountDTO ──────────────────────────────────────

    public function test_create_bank_account_dto_instantiation(): void
    {
        $dto = new CreateBankAccountDTO(
            name: 'Main Checking',
            account_number: '123456789',
            bank_name: 'First National',
            currency: 'USD',
        );

        $this->assertSame('Main Checking', $dto->name);
        $this->assertSame('123456789', $dto->account_number);
    }

    // ── CreateReconciliationDTO ───────────────────────────────────

    public function test_create_reconciliation_dto_instantiation(): void
    {
        $dto = new CreateReconciliationDTO(
            bank_account_id: 1,
            name: 'June Reconciliation',
            start_date: '2025-06-01',
            end_date: '2025-06-30',
        );

        $this->assertSame(1, $dto->bank_account_id);
        $this->assertSame('June Reconciliation', $dto->name);
    }
}
