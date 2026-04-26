<?php

namespace Modules\Accounting\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Modules\Accounting\Domain\Exceptions\UnbalancedJournalEntry;
use Modules\Accounting\Domain\Exceptions\InvalidJournalEntryTransition;
use Modules\Accounting\Domain\Exceptions\InvalidFiscalYearTransition;
use Modules\Accounting\Domain\Exceptions\FiscalYearClosed;

class AccountingExceptionTest extends TestCase
{
    // ── UnbalancedJournalEntry ───────────────────────────────────

    public function test_unbalanced_journal_entry_message(): void
    {
        $exception = new UnbalancedJournalEntry(1000.00, 800.00);
        $this->assertSame('Journal entry is unbalanced: debit 1000 ≠ credit 800', $exception->getMessage());
    }

    public function test_unbalanced_journal_entry_is_runtime_exception(): void
    {
        $exception = new UnbalancedJournalEntry(100.0, 50.0);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    // ── InvalidJournalEntryTransition ────────────────────────────

    public function test_invalid_journal_entry_transition_message(): void
    {
        $exception = new InvalidJournalEntryTransition('cancelled', 'posted');
        $this->assertSame("Cannot transition journal entry from 'cancelled' to 'posted'", $exception->getMessage());
    }

    // ── InvalidFiscalYearTransition ──────────────────────────────

    public function test_invalid_fiscal_year_transition_message(): void
    {
        $exception = new InvalidFiscalYearTransition('locked', 'open');
        $this->assertSame("Cannot transition fiscal year from 'locked' to 'open'", $exception->getMessage());
    }

    // ── FiscalYearClosed ─────────────────────────────────────────

    public function test_fiscal_year_closed_message(): void
    {
        $exception = new FiscalYearClosed('FY 2025');
        $this->assertSame("Fiscal year 'FY 2025' is closed and cannot accept new entries", $exception->getMessage());
    }
}
