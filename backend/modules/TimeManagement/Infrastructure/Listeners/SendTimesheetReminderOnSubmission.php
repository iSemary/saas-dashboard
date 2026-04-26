<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Listeners;

use Modules\TimeManagement\Domain\Events\TimesheetSubmitted;
use Modules\TimeManagement\Domain\Strategies\Reminder\ReminderStrategyInterface;

class SendTimesheetReminderOnSubmission
{
    public function __construct(
        private ReminderStrategyInterface $reminderStrategy
    ) {}

    public function handle(TimesheetSubmitted $event): void
    {
        // Notify approvers that a timesheet needs review
    }
}
