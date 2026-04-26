<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\FiscalYearRepositoryInterface;

class CloseFiscalYear
{
    public function __construct(
        private readonly FiscalYearRepositoryInterface $repository,
    ) {}

    use Modules\Accounting\Domain\ValueObjects\FiscalYearStatus;

    public function execute(int \$id): FiscalYear
    {
        \$fiscalYear = \$this->repository->findOrFail(\$id);
        \$fiscalYear->transitionStatus(FiscalYearStatus::CLOSED);
        return \$fiscalYear->fresh();
    }

}
