<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\Reminder;

interface ReminderStrategyInterface
{
    public function sendTimesheetReminder(string $userId, string $periodEnd): void;
    public function sendClockOutReminder(string $userId): void;
}
