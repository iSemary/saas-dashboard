<?php

namespace Modules\SmsMarketing\Domain\Strategies;

use Modules\SmsMarketing\Domain\Entities\SmCampaign;
use Modules\SmsMarketing\Domain\Entities\SmContact;

interface SmSendingStrategyInterface
{
    public function send(SmCampaign $campaign, SmContact $contact, array $variables = []): string;
}
