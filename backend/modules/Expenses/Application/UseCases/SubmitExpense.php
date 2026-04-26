<?php
declare(strict_types=1);
namespace Modules\Expenses\Application\UseCases;

use Modules\Expenses\Infrastructure\Persistence\ExpenseRepositoryInterface;
use Modules\Expenses\Domain\Strategies\PolicyValidation\PolicyValidationStrategyInterface;
use Modules\Expenses\Domain\Strategies\ExpenseApproval\ExpenseApprovalStrategyInterface;
use Modules\Expenses\Domain\ValueObjects\ExpenseStatus;
use Modules\Expenses\Domain\Entities\Expense;

class SubmitExpense
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $repository,
        private readonly PolicyValidationStrategyInterface $policyValidation,
        private readonly ExpenseApprovalStrategyInterface $approvalStrategy,
    ) {}

    public function execute(int $id): Expense
    {
        $expense = $this->repository->findOrFail($id);
        $this->policyValidation->validate($expense);

        if ($this->approvalStrategy->shouldAutoApprove($expense)) {
            $expense->transitionState(ExpenseStatus::PENDING);
            $expense->approve(auth()->id());
        } else {
            $expense->submit();
        }

        return $expense->fresh();
    }
}
