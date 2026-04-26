<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Application\UseCases\Timesheet;

use Modules\TimeManagement\Domain\Entities\Timesheet;
use Modules\TimeManagement\Domain\ValueObjects\TimesheetStatus;
use Modules\TimeManagement\Infrastructure\Persistence\TimesheetRepositoryInterface;

class RejectTimesheet
{
    public function __construct(
        private TimesheetRepositoryInterface $repository
    ) {}

    public function execute(string $timesheetId, string $rejectedBy, string $reason): Timesheet
    {
        $timesheet = $this->repository->findOrFail($timesheetId);
        $timesheet->transitionStatus(TimesheetStatus::Rejected, $rejectedBy, $reason);
        return $timesheet->fresh();
    }
}
