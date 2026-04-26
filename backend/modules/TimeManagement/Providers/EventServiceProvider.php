<?php

namespace Modules\TimeManagement\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        \Modules\TimeManagement\Domain\Events\TimerStarted::class => [],
        \Modules\TimeManagement\Domain\Events\TimerStopped::class => [],
        \Modules\TimeManagement\Domain\Events\TimeEntryCreated::class => [],
        \Modules\TimeManagement\Domain\Events\TimesheetSubmitted::class => [
            \Modules\TimeManagement\Infrastructure\Listeners\SendTimesheetReminderOnSubmission::class,
        ],
        \Modules\TimeManagement\Domain\Events\TimesheetApproved::class => [],
        \Modules\TimeManagement\Domain\Events\TimesheetRejected::class => [],
        \Modules\TimeManagement\Domain\Events\CalendarEventCreated::class => [],
        \Modules\TimeManagement\Domain\Events\CalendarEventUpdated::class => [],
        \Modules\TimeManagement\Domain\Events\CalendarEventDeleted::class => [],
        \Modules\TimeManagement\Domain\Events\AnomalyDetected::class => [
            \Modules\TimeManagement\Infrastructure\Listeners\NotifyOnAnomalyDetected::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
