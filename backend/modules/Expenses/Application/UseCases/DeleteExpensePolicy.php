<?php
declare(strict_types=1);
namespace Modules\Expenses\Application\UseCases;

use Modules\Expenses\Infrastructure\Persistence\ExpensePolicyRepositoryInterface;
use Modules\Expenses\Domain\Entities\ExpensePolicy;

class DeleteExpensePolicy
{
    public function __construct(
        private readonly ExpensePolicyRepositoryInterface $repository,
    ) {}

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
