<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\BudgetRepositoryInterface;

class UpdateBudget
{
    public function __construct(
        private readonly BudgetRepositoryInterface $repository,
    ) {}

    public function execute(int \$id, array \$data): Budget
    {
        return \$this->repository->update(\$id, \$data);
    }

}
