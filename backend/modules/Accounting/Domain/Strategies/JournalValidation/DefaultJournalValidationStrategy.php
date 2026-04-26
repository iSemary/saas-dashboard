<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Strategies\JournalValidation;

use Modules\Accounting\Domain\Entities\JournalEntry;
use Modules\Accounting\Domain\Exceptions\UnbalancedJournalEntry;

class DefaultJournalValidationStrategy implements JournalValidationStrategyInterface
{
    public function validate(JournalEntry $entry): void
    {
        $totalDebit  = $entry->journalItems()->sum('debit');
        $totalCredit = $entry->journalItems()->sum('credit');

        if (bccomp((string) $totalDebit, (string) $totalCredit, 2) !== 0) {
            throw new UnbalancedJournalEntry($totalDebit, $totalCredit);
        }
    }
}
