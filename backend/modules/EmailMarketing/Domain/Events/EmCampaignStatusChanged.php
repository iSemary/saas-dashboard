<?php

namespace Modules\EmailMarketing\Domain\Events;

use Modules\EmailMarketing\Domain\Entities\EmCampaign;
use Modules\EmailMarketing\Domain\ValueObjects\EmCampaignStatus;

class EmCampaignStatusChanged
{
    public function __construct(
        public readonly EmCampaign $campaign,
        public readonly EmCampaignStatus $from,
        public readonly EmCampaignStatus $to,
    ) {}
}
