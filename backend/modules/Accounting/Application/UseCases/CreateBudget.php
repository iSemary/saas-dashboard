<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\BudgetRepositoryInterface;

class CreateBudget
{
    public function __construct(
        private readonly BudgetRepositoryInterface $repository,
    ) {}

    public function execute(array \$data): Budget
    {
        return \$this->repository->create(\$data);
    }

}
