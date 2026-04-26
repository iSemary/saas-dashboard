<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Application\UseCases\Timesheet;

use Modules\TimeManagement\Application\DTOs\CreateTimesheetData;
use Modules\TimeManagement\Domain\Entities\Timesheet;
use Modules\TimeManagement\Infrastructure\Persistence\TimesheetRepositoryInterface;

class CreateTimesheet
{
    public function __construct(
        private TimesheetRepositoryInterface $repository
    ) {}

    public function execute(CreateTimesheetData $data): Timesheet
    {
        return $this->repository->create($data->toArray());
    }
}
