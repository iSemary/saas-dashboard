<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Application\UseCases\Timesheet;

use Modules\TimeManagement\Domain\Entities\Timesheet;
use Modules\TimeManagement\Domain\ValueObjects\TimesheetStatus;
use Modules\TimeManagement\Infrastructure\Persistence\TimesheetRepositoryInterface;

class ApproveTimesheet
{
    public function __construct(
        private TimesheetRepositoryInterface $repository
    ) {}

    public function execute(string $timesheetId, string $approvedBy): Timesheet
    {
        $timesheet = $this->repository->findOrFail($timesheetId);
        $timesheet->transitionStatus(TimesheetStatus::Approved, $approvedBy);
        return $timesheet->fresh();
    }
}
