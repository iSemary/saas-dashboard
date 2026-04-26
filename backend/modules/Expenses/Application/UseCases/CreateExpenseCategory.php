<?php
declare(strict_types=1);
namespace Modules\Expenses\Application\UseCases;

use Modules\Expenses\Infrastructure\Persistence\ExpenseCategoryRepositoryInterface;
use Modules\Expenses\Domain\Entities\ExpenseCategory;

class CreateExpenseCategory
{
    public function __construct(
        private readonly ExpenseCategoryRepositoryInterface $repository,
    ) {}

    public function execute(array $data): ExpenseCategory
    {
        return $this->repository->create($data);
    }
}
