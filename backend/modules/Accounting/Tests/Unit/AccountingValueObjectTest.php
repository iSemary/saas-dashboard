<?php

namespace Modules\Accounting\Tests\Unit;

use Modules\Accounting\Domain\ValueObjects\AccountType;
use Modules\Accounting\Domain\ValueObjects\AccountSubType;
use Modules\Accounting\Domain\ValueObjects\JournalEntryState;
use Modules\Accounting\Domain\ValueObjects\FiscalYearStatus;
use Modules\Accounting\Domain\ValueObjects\BudgetStatus;
use Modules\Accounting\Domain\ValueObjects\BankTransactionType;
use Modules\Accounting\Domain\ValueObjects\ReconciliationStatus;
use PHPUnit\Framework\TestCase;

class AccountingValueObjectTest extends TestCase
{
    // ── AccountType ────────────────────────────────────────────────

    public function test_account_type_values(): void
    {
        $this->assertSame('asset', AccountType::ASSET->value);
        $this->assertSame('liability', AccountType::LIABILITY->value);
        $this->assertSame('equity', AccountType::EQUITY->value);
        $this->assertSame('income', AccountType::INCOME->value);
        $this->assertSame('expense', AccountType::EXPENSE->value);
    }

    public function test_account_type_from_valid_value(): void
    {
        $type = AccountType::from('asset');
        $this->assertSame(AccountType::ASSET, $type);
    }

    public function test_account_type_from_invalid_throws(): void
    {
        $this->expectException(\ValueError::class);
        AccountType::from('invalid');
    }

    public function test_account_type_all_cases_covered(): void
    {
        $this->assertCount(5, AccountType::cases());
    }

    // ── JournalEntryState ────────────────────────────────────────────

    public function test_journal_entry_state_values(): void
    {
        $this->assertSame('draft', JournalEntryState::DRAFT->value);
        $this->assertSame('posted', JournalEntryState::POSTED->value);
        $this->assertSame('cancelled', JournalEntryState::CANCELLED->value);
    }

    public function test_journal_entry_state_from_valid(): void
    {
        $state = JournalEntryState::from('posted');
        $this->assertSame(JournalEntryState::POSTED, $state);
    }

    public function test_journal_entry_state_try_from_invalid_returns_null(): void
    {
        $this->assertNull(JournalEntryState::tryFrom('nonexistent'));
    }

    public function test_journal_entry_state_all_cases_covered(): void
    {
        $this->assertCount(3, JournalEntryState::cases());
    }

    // ── FiscalYearStatus ────────────────────────────────────────────

    public function test_fiscal_year_status_values(): void
    {
        $this->assertSame('open', FiscalYearStatus::OPEN->value);
        $this->assertSame('closed', FiscalYearStatus::CLOSED->value);
        $this->assertSame('locked', FiscalYearStatus::LOCKED->value);
    }

    public function test_fiscal_year_status_all_cases_covered(): void
    {
        $this->assertCount(3, FiscalYearStatus::cases());
    }

    // ── BudgetStatus ────────────────────────────────────────────────

    public function test_budget_status_values(): void
    {
        $this->assertSame('draft', BudgetStatus::DRAFT->value);
        $this->assertSame('active', BudgetStatus::ACTIVE->value);
        $this->assertSame('archived', BudgetStatus::ARCHIVED->value);
    }

    public function test_budget_status_all_cases_covered(): void
    {
        $this->assertCount(3, BudgetStatus::cases());
    }

    // ── BankTransactionType ────────────────────────────────────────

    public function test_bank_transaction_type_values(): void
    {
        $this->assertSame('debit', BankTransactionType::DEBIT->value);
        $this->assertSame('credit', BankTransactionType::CREDIT->value);
    }

    public function test_bank_transaction_type_all_cases_covered(): void
    {
        $this->assertCount(2, BankTransactionType::cases());
    }

    // ── ReconciliationStatus ────────────────────────────────────────

    public function test_reconciliation_status_values(): void
    {
        $this->assertSame('pending', ReconciliationStatus::PENDING->value);
        $this->assertSame('matched', ReconciliationStatus::MATCHED->value);
        $this->assertSame('unmatched', ReconciliationStatus::UNMATCHED->value);
        $this->assertSame('excluded', ReconciliationStatus::EXCLUDED->value);
    }

    public function test_reconciliation_status_all_cases_covered(): void
    {
        $this->assertCount(4, ReconciliationStatus::cases());
    }

    // ── AccountSubType ─────────────────────────────────────────────

    public function test_account_sub_type_values(): void
    {
        $this->assertSame('current_asset', AccountSubType::CURRENT_ASSET->value);
        $this->assertSame('fixed_asset', AccountSubType::FIXED_ASSET->value);
        $this->assertSame('current_liability', AccountSubType::CURRENT_LIABILITY->value);
        $this->assertSame('operating_income', AccountSubType::OPERATING_INCOME->value);
        $this->assertSame('operating_expense', AccountSubType::OPERATING_EXPENSE->value);
    }

    public function test_account_sub_type_all_cases_covered(): void
    {
        $this->assertCount(11, AccountSubType::cases());
    }
}
