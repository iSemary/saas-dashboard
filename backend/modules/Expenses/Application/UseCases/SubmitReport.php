<?php
declare(strict_types=1);
namespace Modules\Expenses\Application\UseCases;

use Modules\Expenses\Infrastructure\Persistence\ExpenseReportRepositoryInterface;
use Modules\Expenses\Domain\ValueObjects\ReportStatus;
use Modules\Expenses\Domain\Entities\ExpenseReport;

class SubmitReport
{
    public function __construct(
        private readonly ExpenseReportRepositoryInterface $repository,
    ) {}

    public function execute(int $id): ExpenseReport
    {
        $report = $this->repository->findOrFail($id);
        $report->recalculateTotal();
        $report->submit();
        return $report->fresh();
    }
}
