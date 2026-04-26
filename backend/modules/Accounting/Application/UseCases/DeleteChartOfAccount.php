<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\ChartOfAccountRepositoryInterface;

class DeleteChartOfAccount
{
    public function __construct(
        private readonly ChartOfAccountRepositoryInterface $repository,
    ) {}

    public function execute(int \$id): bool
    {
        return \$this->repository->delete(\$id);
    }

}
