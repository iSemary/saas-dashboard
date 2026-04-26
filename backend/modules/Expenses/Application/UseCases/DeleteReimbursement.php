<?php
declare(strict_types=1);
namespace Modules\Expenses\Application\UseCases;

use Modules\Expenses\Infrastructure\Persistence\ReimbursementRepositoryInterface;
use Modules\Expenses\Domain\Entities\Reimbursement;

class DeleteReimbursement
{
    public function __construct(
        private readonly ReimbursementRepositoryInterface $repository,
    ) {}

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
