<?php
declare(strict_types=1);
namespace Modules\Expenses\Application\UseCases;

use Modules\Expenses\Infrastructure\Persistence\ExpenseRepositoryInterface;
use Modules\Expenses\Domain\Entities\Expense;

class RejectExpense
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $repository,
    ) {}

    public function execute(int $id, string $reason = ''): Expense
    {
        $expense = $this->repository->findOrFail($id);
        $expense->reject(auth()->id(), $reason);
        return $expense->fresh();
    }
}
