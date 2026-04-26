<?php

namespace Modules\EmailMarketing\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\EmailMarketing\Domain\Events\EmCampaignCreated;
use Modules\EmailMarketing\Domain\Events\EmCampaignSent;
use Modules\EmailMarketing\Domain\Events\EmCampaignStatusChanged;
use Modules\EmailMarketing\Domain\Events\EmContactCreated;
use Modules\EmailMarketing\Domain\Events\EmContactUnsubscribed;
use Modules\EmailMarketing\Infrastructure\Listeners\TriggerAutomationOnCampaignEvent;
use Modules\EmailMarketing\Infrastructure\Listeners\UpdateCampaignStatsOnLogUpdate;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        EmCampaignCreated::class => [
            TriggerAutomationOnCampaignEvent::class,
        ],
        EmCampaignSent::class => [
            TriggerAutomationOnCampaignEvent::class,
            UpdateCampaignStatsOnLogUpdate::class,
        ],
        EmCampaignStatusChanged::class => [
            TriggerAutomationOnCampaignEvent::class,
        ],
        EmContactCreated::class => [
            TriggerAutomationOnCampaignEvent::class,
        ],
        EmContactUnsubscribed::class => [
            TriggerAutomationOnCampaignEvent::class,
        ],
    ];
}
