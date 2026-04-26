<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Strategies\PolicyValidation;

use Modules\Expenses\Domain\Entities\Expense;
use Modules\Expenses\Domain\Entities\ExpensePolicy;
use Modules\Expenses\Domain\Exceptions\PolicyViolation;

class DefaultPolicyValidationStrategy implements PolicyValidationStrategyInterface
{
    public function validate(Expense $expense): void
    {
        $policies = ExpensePolicy::where('is_active', true)->get();

        foreach ($policies as $policy) {
            match($policy->type) {
                'max_amount' => $this->checkMaxAmount($expense, $policy),
                'receipt_required' => $this->checkReceiptRequired($expense, $policy),
                default => null,
            };
        }
    }

    private function checkMaxAmount(Expense $expense, ExpensePolicy $policy): void
    {
        $maxAmount = (float) ($policy->rules['max_amount'] ?? 0);
        $appliesToCategory = empty($policy->rules['category_id']) || $policy->rules['category_id'] == $expense->category_id;

        if ($appliesToCategory && $maxAmount > 0 && $expense->amount > $maxAmount) {
            throw new PolicyViolation("Expense amount {$expense->amount} exceeds policy limit of {$maxAmount}");
        }
    }

    private function checkReceiptRequired(Expense $expense, ExpensePolicy $policy): void
    {
        $threshold = (float) ($policy->rules['min_amount'] ?? 0);
        $appliesToCategory = empty($policy->rules['category_id']) || $policy->rules['category_id'] == $expense->category_id;

        if ($appliesToCategory && $expense->amount >= $threshold && !$expense->receipt) {
            throw new PolicyViolation("Receipt is required for expenses >= {$threshold}");
        }
    }
}
