<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\TimeValidation;

interface TimeValidationStrategyInterface
{
    public function validateTimeEntry(array $data): bool;
    public function validateTimesheetHours(int $totalMinutes, int $expectedMinutes): bool;
}
