<?php

declare(strict_types=1);

namespace Modules\Expenses\Infrastructure\Listeners;

use Modules\Expenses\Domain\Events\ExpenseReimbursed;
use Modules\Accounting\Domain\Entities\JournalEntry;
use Modules\Accounting\Domain\Entities\JournalItem;
use Modules\Accounting\Domain\ValueObjects\JournalEntryState;

class CreateJournalEntryOnReimbursement
{
    public function handle(ExpenseReimbursed $event): void
    {
        $expense = $event->entity;

        if (!($expense instanceof \Modules\Expenses\Domain\Entities\Expense)) {
            return;
        }

        // Create journal entry: debit accounts payable, credit cash
        $entry = JournalEntry::create([
            'entry_number' => 'REIMB-' . $expense->id,
            'entry_date' => now()->toDateString(),
            'state' => JournalEntryState::DRAFT->value,
            'description' => 'Reimbursement: ' . $expense->title,
            'currency' => $expense->currency,
            'total_debit' => $expense->amount,
            'total_credit' => $expense->amount,
            'created_by' => $expense->reimbursed_by ?? $expense->created_by,
        ]);

        // Debit: Accounts Payable
        JournalItem::create([
            'journal_entry_id' => $entry->id,
            'account_id' => 1, // Default: Accounts Payable
            'debit' => $expense->amount,
            'credit' => 0,
            'description' => 'Reimbursement for: ' . $expense->title,
        ]);

        // Credit: Cash/Bank
        JournalItem::create([
            'journal_entry_id' => $entry->id,
            'account_id' => 1, // Default: Cash/Bank
            'debit' => 0,
            'credit' => $expense->amount,
            'description' => 'Cash out for: ' . $expense->title,
        ]);
    }
}
