<?php

declare(strict_types=1);

namespace Modules\Expenses\Infrastructure\Listeners;

use Modules\Expenses\Domain\Events\ExpenseApproved;
use Modules\Accounting\Domain\Entities\JournalEntry;
use Modules\Accounting\Domain\Entities\JournalItem;
use Modules\Accounting\Domain\ValueObjects\JournalEntryState;

class CreateJournalEntryOnExpenseApproved
{
    public function handle(ExpenseApproved $event): void
    {
        $expense = $event->entity;

        if (!($expense instanceof \Modules\Expenses\Domain\Entities\Expense)) {
            return;
        }

        // Create journal entry: debit expense account, credit cash/payable
        $entry = JournalEntry::create([
            'entry_number' => 'EXP-' . $expense->id,
            'entry_date' => $expense->date,
            'state' => JournalEntryState::DRAFT->value,
            'description' => 'Expense: ' . $expense->title,
            'currency' => $expense->currency,
            'total_debit' => $expense->amount,
            'total_credit' => $expense->amount,
            'created_by' => $expense->approved_by ?? $expense->created_by,
        ]);

        // Debit: Expense account (from category)
        JournalItem::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $expense->category->default_account_id ?? null,
            'debit' => $expense->amount,
            'credit' => 0,
            'description' => $expense->title,
        ]);

        // Credit: Cash or Accounts Payable (account ID 1 as default)
        JournalItem::create([
            'journal_entry_id' => $entry->id,
            'account_id' => 1, // Default: Cash/Bank
            'debit' => 0,
            'credit' => $expense->amount,
            'description' => 'Payment for: ' . $expense->title,
        ]);
    }
}
