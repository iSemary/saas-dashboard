<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\BankAccountRepositoryInterface;

class UpdateBankAccount
{
    public function __construct(
        private readonly BankAccountRepositoryInterface $repository,
    ) {}

    public function execute(int \$id, array \$data): BankAccount
    {
        return \$this->repository->update(\$id, \$data);
    }

}
