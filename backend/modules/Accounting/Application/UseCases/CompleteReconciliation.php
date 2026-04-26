<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\ReconciliationRepositoryInterface;

class CompleteReconciliation
{
    public function __construct(
        private readonly ReconciliationRepositoryInterface $repository,
    ) {}

    public function execute(int \$id): Reconciliation
    {
        \$reconciliation = \$this->repository->findOrFail(\$id);
        \$reconciliation->complete();
        return \$reconciliation->fresh();
    }

}
