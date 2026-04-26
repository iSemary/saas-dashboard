<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\TimeValidation;

class DefaultTimeValidationStrategy implements TimeValidationStrategyInterface
{
    public function validateTimeEntry(array $data): bool
    {
        return !empty($data['user_id']) && !empty($data['date'])
            && ($data['duration_minutes'] ?? 0) > 0;
    }

    public function validateTimesheetHours(int $totalMinutes, int $expectedMinutes): bool
    {
        return $totalMinutes <= $expectedMinutes * 2; // Allow up to 2x expected
    }
}
