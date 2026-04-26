<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Exceptions;

class UnbalancedJournalEntry extends \RuntimeException
{
    public function __construct(float $totalDebit, float $totalCredit)
    {
        parent::__construct("Journal entry is unbalanced: debit {$totalDebit} ≠ credit {$totalCredit}");
    }
}
