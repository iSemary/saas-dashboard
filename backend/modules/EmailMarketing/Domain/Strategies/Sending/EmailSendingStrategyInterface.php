<?php

namespace Modules\EmailMarketing\Domain\Strategies\Sending;

use Modules\EmailMarketing\Domain\Entities\EmCampaign;
use Modules\EmailMarketing\Domain\Entities\EmContact;

interface EmailSendingStrategyInterface
{
    public function send(EmCampaign $campaign, EmContact $contact, array $variables = []): string;
}
