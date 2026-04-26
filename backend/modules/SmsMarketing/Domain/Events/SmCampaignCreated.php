<?php

namespace Modules\SmsMarketing\Domain\Events;

use Modules\SmsMarketing\Domain\Entities\SmCampaign;

class SmCampaignCreated
{
    public function __construct(public readonly SmCampaign $campaign) {}
}
