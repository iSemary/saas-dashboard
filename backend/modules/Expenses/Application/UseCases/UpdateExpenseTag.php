<?php
declare(strict_types=1);
namespace Modules\Expenses\Application\UseCases;

use Modules\Expenses\Infrastructure\Persistence\ExpenseTagRepositoryInterface;
use Modules\Expenses\Domain\Entities\ExpenseTag;

class UpdateExpenseTag
{
    public function __construct(
        private readonly ExpenseTagRepositoryInterface $repository,
    ) {}

    public function execute(int $id, array $data): ExpenseTag
    {
        return $this->repository->update($id, $data);
    }
}
