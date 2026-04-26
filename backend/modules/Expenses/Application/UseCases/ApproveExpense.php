<?php
declare(strict_types=1);
namespace Modules\Expenses\Application\UseCases;

use Modules\Expenses\Infrastructure\Persistence\ExpenseRepositoryInterface;
use Modules\Expenses\Domain\Entities\Expense;

class ApproveExpense
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $repository,
    ) {}

    public function execute(int $id): Expense
    {
        $expense = $this->repository->findOrFail($id);
        $expense->approve(auth()->id());
        return $expense->fresh();
    }
}
