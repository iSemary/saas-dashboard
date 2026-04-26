<?php

namespace Modules\Accounting\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Modules\Accounting\Domain\Events\JournalEntryCreated;
use Modules\Accounting\Domain\Events\JournalEntryPosted;
use Modules\Accounting\Domain\Events\FiscalYearCreated;
use Modules\Accounting\Domain\Events\BudgetCreated;

class AccountingEventTest extends TestCase
{
    // ── JournalEntryCreated ──────────────────────────────────────

    public function test_journal_entry_created_stores_entry(): void
    {
        $entry = $this->createMock(\Modules\Accounting\Domain\Entities\JournalEntry::class);
        $event = new JournalEntryCreated($entry);

        $this->assertSame($entry, $event->journalEntry);
    }

    // ── JournalEntryPosted ───────────────────────────────────────

    public function test_journal_entry_posted_stores_entry_and_states(): void
    {
        $entry = $this->createMock(\Modules\Accounting\Domain\Entities\JournalEntry::class);
        $event = new JournalEntryPosted($entry, 'draft', 'posted');

        $this->assertSame($entry, $event->journalEntry);
        $this->assertSame('draft', $event->oldState);
        $this->assertSame('posted', $event->newState);
    }

    public function test_journal_entry_posted_states_are_optional(): void
    {
        $entry = $this->createMock(\Modules\Accounting\Domain\Entities\JournalEntry::class);
        $event = new JournalEntryPosted($entry);

        $this->assertNull($event->oldState);
        $this->assertNull($event->newState);
    }

    // ── FiscalYearCreated ────────────────────────────────────────

    public function test_fiscal_year_created_stores_fiscal_year(): void
    {
        $fy = $this->createMock(\Modules\Accounting\Domain\Entities\FiscalYear::class);
        $event = new FiscalYearCreated($fy);

        $this->assertSame($fy, $event->fiscalYear);
    }

    // ── BudgetCreated ────────────────────────────────────────────

    public function test_budget_created_stores_budget(): void
    {
        $budget = $this->createMock(\Modules\Accounting\Domain\Entities\Budget::class);
        $event = new BudgetCreated($budget);

        $this->assertSame($budget, $event->budget);
    }
}
