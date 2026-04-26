<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\ChartOfAccountRepositoryInterface;

class CreateChartOfAccount
{
    public function __construct(
        private readonly ChartOfAccountRepositoryInterface $repository,
    ) {}

    public function execute(array \$data): ChartOfAccount
    {
        return \$this->repository->create(\$data);
    }

}
