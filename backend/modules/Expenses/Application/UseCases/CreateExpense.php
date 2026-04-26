<?php
declare(strict_types=1);
namespace Modules\Expenses\Application\UseCases;

use Modules\Expenses\Infrastructure\Persistence\ExpenseRepositoryInterface;
use Modules\Expenses\Domain\Entities\Expense;

class CreateExpense
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $repository,
    ) {}

    public function execute(array $data): Expense
    {
        return $this->repository->create($data);
    }
}
