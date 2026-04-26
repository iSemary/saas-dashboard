<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\ConflictDetection;

use Modules\TimeManagement\Domain\Entities\CalendarEvent;

class DefaultConflictDetectionStrategy implements ConflictDetectionStrategyInterface
{
    public function detectConflicts(string $userId, string $startsAt, string $endsAt, ?string $excludeEventId = null): array
    {
        $query = CalendarEvent::where('user_id', $userId)
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt);

        if ($excludeEventId) {
            $query->where('id', '!=', $excludeEventId);
        }

        return $query->get()->all();
    }
}
