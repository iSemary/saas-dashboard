<?php

namespace Modules\EmailMarketing\Domain\Events;

use Modules\EmailMarketing\Domain\Entities\EmCampaign;

class EmCampaignCreated
{
    public function __construct(public readonly EmCampaign $campaign) {}
}
