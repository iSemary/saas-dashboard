<?php

namespace Modules\SmsMarketing\Domain\Strategies\Sending;

use Modules\SmsMarketing\Domain\Entities\SmCampaign;
use Modules\SmsMarketing\Domain\Entities\SmContact;

interface SmsSendingStrategyInterface
{
    public function send(SmCampaign $campaign, SmContact $contact, array $variables = []): string;
}
