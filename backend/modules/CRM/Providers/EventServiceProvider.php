<?php

namespace Modules\CRM\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\CRM\Domain\Events;
use Modules\CRM\Infrastructure\Listeners;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        Events\LeadCreated::class => [
            Listeners\TriggerAutomationOnLeadCreated::class,
            Listeners\CreateActivityOnLeadCreated::class,
            Listeners\DispatchWebhookOnDomainEvent::class,
            Listeners\LogActivityOnDomainEvent::class,
        ],
        Events\LeadStatusChanged::class => [
            Listeners\DispatchWebhookOnDomainEvent::class,
            Listeners\LogActivityOnDomainEvent::class,
        ],
        Events\LeadConverted::class => [
            Listeners\DispatchWebhookOnDomainEvent::class,
            Listeners\LogActivityOnDomainEvent::class,
        ],
        Events\OpportunityCreated::class => [
            Listeners\DispatchWebhookOnDomainEvent::class,
            Listeners\LogActivityOnDomainEvent::class,
        ],
        Events\OpportunityStageChanged::class => [
            Listeners\TriggerAutomationOnOpportunityStageChanged::class,
            Listeners\DispatchWebhookOnDomainEvent::class,
            Listeners\LogActivityOnDomainEvent::class,
        ],
        Events\OpportunityClosedWon::class => [
            Listeners\DispatchWebhookOnDomainEvent::class,
            Listeners\LogActivityOnDomainEvent::class,
        ],
        Events\OpportunityClosedLost::class => [
            Listeners\DispatchWebhookOnDomainEvent::class,
            Listeners\LogActivityOnDomainEvent::class,
        ],
        Events\ActivityCreated::class => [
            Listeners\DispatchWebhookOnDomainEvent::class,
        ],
        Events\ActivityCompleted::class => [
            Listeners\DispatchWebhookOnDomainEvent::class,
        ],
        Events\ContactCreated::class => [
            Listeners\DispatchWebhookOnDomainEvent::class,
            Listeners\LogActivityOnDomainEvent::class,
        ],
        Events\CompanyCreated::class => [
            Listeners\DispatchWebhookOnDomainEvent::class,
            Listeners\LogActivityOnDomainEvent::class,
        ],
        Events\EntityAssigned::class => [
            Listeners\SendNotificationOnAssignment::class,
            Listeners\DispatchWebhookOnDomainEvent::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = false;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void
    {
        //
    }
}
