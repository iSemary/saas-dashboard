<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\TaxRateRepositoryInterface;

class CreateTaxRate
{
    public function __construct(
        private readonly TaxRateRepositoryInterface $repository,
    ) {}

    public function execute(array \$data): TaxRate
    {
        return \$this->repository->create(\$data);
    }

}
