<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Strategies\ExpenseApproval;

use Modules\Expenses\Domain\Entities\Expense;
use Modules\Expenses\Domain\Entities\ExpensePolicy;

class DefaultExpenseApprovalStrategy implements ExpenseApprovalStrategyInterface
{
    public function shouldAutoApprove(Expense $expense): bool
    {
        $autoApprovalPolicies = ExpensePolicy::where('type', 'auto_approval')
            ->where('is_active', true)
            ->get();

        foreach ($autoApprovalPolicies as $policy) {
            $threshold = (float) ($policy->rules['max_amount'] ?? PHP_FLOAT_MAX);
            if ($expense->amount <= $threshold) {
                return true;
            }
        }

        return false;
    }

    public function requiresManagerApproval(Expense $expense): bool
    {
        return !$this->shouldAutoApprove($expense);
    }
}
