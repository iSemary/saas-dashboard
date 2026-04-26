<?php

namespace Modules\SmsMarketing\Domain\Events;

use Modules\SmsMarketing\Domain\Entities\SmCampaign;
use Modules\SmsMarketing\Domain\ValueObjects\SmCampaignStatus;

class SmCampaignStatusChanged
{
    public function __construct(
        public readonly SmCampaign $campaign,
        public readonly SmCampaignStatus $from,
        public readonly SmCampaignStatus $to,
    ) {}
}
