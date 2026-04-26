<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\TaxRateRepositoryInterface;

class DeleteTaxRate
{
    public function __construct(
        private readonly TaxRateRepositoryInterface $repository,
    ) {}

    public function execute(int \$id): bool
    {
        return \$this->repository->delete(\$id);
    }

}
