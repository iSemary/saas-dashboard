<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Listeners;

use Modules\TimeManagement\Domain\Events\AnomalyDetected;
use Modules\TimeManagement\Domain\Strategies\Reminder\ReminderStrategyInterface;

class NotifyOnAnomalyDetected
{
    public function __construct(
        private ReminderStrategyInterface $reminderStrategy
    ) {}

    public function handle(AnomalyDetected $event): void
    {
        $this->reminderStrategy->sendClockOutReminder($event->userId);
    }
}
