<?php

namespace Modules\Expenses\Tests\Unit;

use Modules\Expenses\Domain\ValueObjects\ExpenseStatus;
use Modules\Expenses\Domain\ValueObjects\ReportStatus;
use Modules\Expenses\Domain\ValueObjects\PolicyType;
use Modules\Expenses\Domain\ValueObjects\ReimbursementStatus;
use Modules\Expenses\Domain\ValueObjects\ExpenseCurrency;
use PHPUnit\Framework\TestCase;

class ExpensesValueObjectTest extends TestCase
{
    // ── ExpenseStatus ─────────────────────────────────────────────

    public function test_expense_status_values(): void
    {
        $this->assertSame('draft', ExpenseStatus::DRAFT->value);
        $this->assertSame('pending', ExpenseStatus::PENDING->value);
        $this->assertSame('approved', ExpenseStatus::APPROVED->value);
        $this->assertSame('rejected', ExpenseStatus::REJECTED->value);
        $this->assertSame('reimbursed', ExpenseStatus::REIMBURSED->value);
        $this->assertSame('cancelled', ExpenseStatus::CANCELLED->value);
    }

    public function test_expense_status_from_valid(): void
    {
        $status = ExpenseStatus::from('approved');
        $this->assertSame(ExpenseStatus::APPROVED, $status);
    }

    public function test_expense_status_try_from_invalid_returns_null(): void
    {
        $this->assertNull(ExpenseStatus::tryFrom('nonexistent'));
    }

    public function test_expense_status_all_cases_covered(): void
    {
        $this->assertCount(6, ExpenseStatus::cases());
    }

    // ── ReportStatus ──────────────────────────────────────────────

    public function test_report_status_values(): void
    {
        $this->assertSame('draft', ReportStatus::DRAFT->value);
        $this->assertSame('submitted', ReportStatus::SUBMITTED->value);
        $this->assertSame('approved', ReportStatus::APPROVED->value);
        $this->assertSame('rejected', ReportStatus::REJECTED->value);
        $this->assertSame('reimbursed', ReportStatus::REIMBURSED->value);
    }

    public function test_report_status_all_cases_covered(): void
    {
        $this->assertCount(5, ReportStatus::cases());
    }

    // ── PolicyType ─────────────────────────────────────────────────

    public function test_policy_type_values(): void
    {
        $this->assertSame('max_amount', PolicyType::MAX_AMOUNT->value);
        $this->assertSame('receipt_required', PolicyType::RECEIPT_REQUIRED->value);
        $this->assertSame('auto_approval', PolicyType::AUTO_APPROVAL->value);
        $this->assertSame('category_restriction', PolicyType::CATEGORY_RESTRICTION->value);
        $this->assertSame('duplicate_check', PolicyType::DUPLICATE_CHECK->value);
    }

    public function test_policy_type_all_cases_covered(): void
    {
        $this->assertCount(5, PolicyType::cases());
    }

    // ── ReimbursementStatus ───────────────────────────────────────

    public function test_reimbursement_status_values(): void
    {
        $this->assertSame('pending', ReimbursementStatus::PENDING->value);
        $this->assertSame('processing', ReimbursementStatus::PROCESSING->value);
        $this->assertSame('completed', ReimbursementStatus::COMPLETED->value);
        $this->assertSame('failed', ReimbursementStatus::FAILED->value);
    }

    public function test_reimbursement_status_all_cases_covered(): void
    {
        $this->assertCount(4, ReimbursementStatus::cases());
    }

    // ── ExpenseCurrency ────────────────────────────────────────────

    public function test_expense_currency_values(): void
    {
        $this->assertSame('USD', ExpenseCurrency::USD->value);
        $this->assertSame('EUR', ExpenseCurrency::EUR->value);
        $this->assertSame('GBP', ExpenseCurrency::GBP->value);
        $this->assertSame('SAR', ExpenseCurrency::SAR->value);
        $this->assertSame('AED', ExpenseCurrency::AED->value);
    }

    public function test_expense_currency_all_cases_covered(): void
    {
        $this->assertCount(5, ExpenseCurrency::cases());
    }
}
