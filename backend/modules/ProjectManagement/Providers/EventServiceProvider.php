<?php

namespace Modules\ProjectManagement\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        \Modules\ProjectManagement\Domain\Events\ProjectCreated::class => [
            \Modules\ProjectManagement\Infrastructure\Listeners\CreateDefaultBoardColumnsOnProjectCreated::class,
        ],
        \Modules\ProjectManagement\Domain\Events\ProjectStatusChanged::class => [
            \Modules\ProjectManagement\Infrastructure\Listeners\NotifyOnProjectStatusChanged::class,
        ],
        \Modules\ProjectManagement\Domain\Events\TaskCreated::class => [],
        \Modules\ProjectManagement\Domain\Events\TaskMovedToColumn::class => [
            \Modules\ProjectManagement\Infrastructure\Listeners\TriggerAutomationOnTaskMoved::class,
        ],
        \Modules\ProjectManagement\Domain\Events\TaskStatusChanged::class => [],
        \Modules\ProjectManagement\Domain\Events\TaskAssigned::class => [
            \Modules\ProjectManagement\Infrastructure\Listeners\NotifyOnTaskAssigned::class,
        ],
        \Modules\ProjectManagement\Domain\Events\MilestoneCompleted::class => [
            \Modules\ProjectManagement\Infrastructure\Listeners\NotifyOnMilestoneCompleted::class,
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
