<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\ReconciliationRepositoryInterface;

class CreateReconciliation
{
    public function __construct(
        private readonly ReconciliationRepositoryInterface $repository,
    ) {}

    public function execute(array \$data): Reconciliation
    {
        return \$this->repository->create(\$data);
    }

}
