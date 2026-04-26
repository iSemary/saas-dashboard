<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Strategies\ExpenseApproval;

use Modules\Expenses\Domain\Entities\Expense;

interface ExpenseApprovalStrategyInterface
{
    public function shouldAutoApprove(Expense $expense): bool;
    public function requiresManagerApproval(Expense $expense): bool;
}
