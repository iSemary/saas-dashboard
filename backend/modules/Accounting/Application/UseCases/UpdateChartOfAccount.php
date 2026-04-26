<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\ChartOfAccountRepositoryInterface;

class UpdateChartOfAccount
{
    public function __construct(
        private readonly ChartOfAccountRepositoryInterface $repository,
    ) {}

    public function execute(int \$id, array \$data): ChartOfAccount
    {
        return \$this->repository->update(\$id, \$data);
    }

}
