<?php

namespace Modules\Survey\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        \Modules\Survey\Domain\Events\SurveyCreated::class => [],
        \Modules\Survey\Domain\Events\SurveyPublished::class => [],
        \Modules\Survey\Domain\Events\SurveyClosed::class => [],
        \Modules\Survey\Domain\Events\SurveyResponseCreated::class => [
            \Modules\Survey\Infrastructure\Listeners\TriggerAutomationOnResponseCreated::class,
        ],
        \Modules\Survey\Domain\Events\SurveyResponseCompleted::class => [
            \Modules\Survey\Infrastructure\Listeners\TriggerAutomationOnResponseCompleted::class,
            \Modules\Survey\Infrastructure\Listeners\DispatchWebhookOnResponseCompleted::class,
            \Modules\Survey\Infrastructure\Listeners\CreateCrmActivityOnResponseCompleted::class,
            \Modules\Survey\Infrastructure\Listeners\BroadcastResponseToWebSocket::class,
        ],
        \Modules\Survey\Domain\Events\SurveyQuestionAnswered::class => [
            \Modules\Survey\Infrastructure\Listeners\TriggerAutomationOnQuestionAnswered::class,
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
