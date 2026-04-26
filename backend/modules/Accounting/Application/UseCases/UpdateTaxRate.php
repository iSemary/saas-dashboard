<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\TaxRateRepositoryInterface;

class UpdateTaxRate
{
    public function __construct(
        private readonly TaxRateRepositoryInterface $repository,
    ) {}

    public function execute(int \$id, array \$data): TaxRate
    {
        return \$this->repository->update(\$id, \$data);
    }

}
