<?php

namespace Modules\SmsMarketing\Domain\Events;

use Modules\SmsMarketing\Domain\Entities\SmCampaign;

class SmCampaignSent
{
    public function __construct(public readonly SmCampaign $campaign) {}
}
