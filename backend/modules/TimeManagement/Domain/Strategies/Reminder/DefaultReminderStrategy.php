<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\Reminder;

class DefaultReminderStrategy implements ReminderStrategyInterface
{
    public function sendTimesheetReminder(string $userId, string $periodEnd): void
    {
        // No-op default — override with real notification dispatch
    }

    public function sendClockOutReminder(string $userId): void
    {
        // No-op default
    }
}
