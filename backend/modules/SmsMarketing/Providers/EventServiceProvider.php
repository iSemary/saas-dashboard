<?php

namespace Modules\SmsMarketing\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\SmsMarketing\Domain\Events\SmCampaignCreated;
use Modules\SmsMarketing\Domain\Events\SmCampaignSent;
use Modules\SmsMarketing\Domain\Events\SmCampaignStatusChanged;
use Modules\SmsMarketing\Domain\Events\SmContactCreated;
use Modules\SmsMarketing\Domain\Events\SmContactOptedOut;
use Modules\SmsMarketing\Infrastructure\Listeners\TriggerAutomationOnCampaignEvent;
use Modules\SmsMarketing\Infrastructure\Listeners\UpdateCampaignStatsOnLogUpdate;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SmCampaignCreated::class => [
            TriggerAutomationOnCampaignEvent::class,
        ],
        SmCampaignSent::class => [
            TriggerAutomationOnCampaignEvent::class,
            UpdateCampaignStatsOnLogUpdate::class,
        ],
        SmCampaignStatusChanged::class => [
            TriggerAutomationOnCampaignEvent::class,
        ],
        SmContactCreated::class => [
            TriggerAutomationOnCampaignEvent::class,
        ],
        SmContactOptedOut::class => [
            TriggerAutomationOnCampaignEvent::class,
        ],
    ];
}
