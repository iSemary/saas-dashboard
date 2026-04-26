<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Application\UseCases\Calendar;

use Modules\TimeManagement\Application\DTOs\CreateCalendarEventData;
use Modules\TimeManagement\Domain\Entities\CalendarEvent;
use Modules\TimeManagement\Domain\Strategies\ConflictDetection\ConflictDetectionStrategyInterface;
use Modules\TimeManagement\Domain\Exceptions\CalendarConflictDetected;
use Modules\TimeManagement\Infrastructure\Persistence\CalendarEventRepositoryInterface;

class CreateCalendarEvent
{
    public function __construct(
        private CalendarEventRepositoryInterface $repository,
        private ConflictDetectionStrategyInterface $conflictDetection,
    ) {}

    public function execute(CreateCalendarEventData $data, bool $skipConflictCheck = false): CalendarEvent
    {
        if (!$skipConflictCheck) {
            $conflicts = $this->conflictDetection->detectConflicts(
                $data->userId,
                $data->startsAt,
                $data->endsAt,
            );

            if (count($conflicts) > 0) {
                throw new CalendarConflictDetected(count($conflicts));
            }
        }

        return $this->repository->create($data->toArray());
    }
}
