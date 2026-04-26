<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Strategies\PolicyValidation;

use Modules\Expenses\Domain\Entities\Expense;

interface PolicyValidationStrategyInterface
{
    public function validate(Expense $expense): void;
}
