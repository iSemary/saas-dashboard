<?php

namespace Modules\EmailMarketing\Domain\Strategies;

use Modules\EmailMarketing\Domain\Entities\EmCampaign;
use Modules\EmailMarketing\Domain\Entities\EmContact;

interface EmSendingStrategyInterface
{
    public function send(EmCampaign $campaign, EmContact $contact, array $variables = []): string;
}
