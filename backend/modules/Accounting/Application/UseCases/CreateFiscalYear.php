<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\FiscalYearRepositoryInterface;

class CreateFiscalYear
{
    public function __construct(
        private readonly FiscalYearRepositoryInterface $repository,
    ) {}

    public function execute(array \$data): FiscalYear
    {
        return \$this->repository->create(\$data);
    }

}
