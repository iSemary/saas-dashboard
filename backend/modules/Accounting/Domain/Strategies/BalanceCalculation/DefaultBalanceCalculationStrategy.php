<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Strategies\BalanceCalculation;

use Modules\Accounting\Domain\Entities\ChartOfAccount;

class DefaultBalanceCalculationStrategy implements BalanceCalculationStrategyInterface
{
    public function calculate(ChartOfAccount $account): float
    {
        $debitTotal  = (float) $account->journalItems()->sum('debit');
        $creditTotal = (float) $account->journalItems()->sum('credit');

        if ($account->isDebitAccount()) {
            return (float) $account->opening_balance + $debitTotal - $creditTotal;
        }

        return (float) $account->opening_balance + $creditTotal - $debitTotal;
    }
}
