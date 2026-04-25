<?php

namespace Modules\HR\Application\UseCases\Expenses;

use Modules\HR\Domain\Entities\ExpenseClaim;
use Modules\HR\Infrastructure\Persistence\ExpenseClaimRepositoryInterface;

class SubmitExpenseClaimUseCase
{
    public function __construct(
        protected ExpenseClaimRepositoryInterface $repository,
    ) {}

    public function execute(array $data): ExpenseClaim
    {
        $data['status'] = $data['status'] ?? 'submitted';
        return $this->repository->create($data);
    }
}
