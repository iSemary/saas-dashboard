<?php
declare(strict_types=1);
namespace Modules\Expenses\Application\UseCases;

use Modules\Expenses\Infrastructure\Persistence\ReimbursementRepositoryInterface;
use Modules\Expenses\Domain\Entities\Reimbursement;

class CreateReimbursement
{
    public function __construct(
        private readonly ReimbursementRepositoryInterface $repository,
    ) {}

    public function execute(array $data): Reimbursement
    {
        return $this->repository->create($data);
    }
}
