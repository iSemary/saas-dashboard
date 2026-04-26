<?php
declare(strict_types=1);
namespace Modules\Expenses\Application\UseCases;

use Modules\Expenses\Infrastructure\Persistence\ExpenseReportRepositoryInterface;
use Modules\Expenses\Domain\Entities\ExpenseReport;

class CreateExpenseReport
{
    public function __construct(
        private readonly ExpenseReportRepositoryInterface $repository,
    ) {}

    public function execute(array $data): ExpenseReport
    {
        return $this->repository->create($data);
    }
}
