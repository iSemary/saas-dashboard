<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\BudgetRepositoryInterface;

class DeleteBudget
{
    public function __construct(
        private readonly BudgetRepositoryInterface $repository,
    ) {}

    public function execute(int \$id): bool
    {
        return \$this->repository->delete(\$id);
    }

}
