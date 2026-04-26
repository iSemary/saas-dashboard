<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\BankAccountRepositoryInterface;

class CreateBankAccount
{
    public function __construct(
        private readonly BankAccountRepositoryInterface $repository,
    ) {}

    public function execute(array \$data): BankAccount
    {
        return \$this->repository->create(\$data);
    }

}
