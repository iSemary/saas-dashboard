<?php
declare(strict_types=1);
namespace Modules\Expenses\Application\UseCases;

use Modules\Expenses\Infrastructure\Persistence\ReimbursementRepositoryInterface;
use Modules\Expenses\Domain\Entities\Reimbursement;

class UpdateReimbursement
{
    public function __construct(
        private readonly ReimbursementRepositoryInterface $repository,
    ) {}

    public function execute(int $id, array $data): Reimbursement
    {
        return $this->repository->update($id, $data);
    }
}
