<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Strategies\BalanceCalculation;

use Modules\Accounting\Domain\Entities\ChartOfAccount;

interface BalanceCalculationStrategyInterface
{
    public function calculate(ChartOfAccount $account): float;
}
