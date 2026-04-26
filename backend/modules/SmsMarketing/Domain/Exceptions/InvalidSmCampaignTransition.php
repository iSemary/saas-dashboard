<?php

namespace Modules\SmsMarketing\Domain\Exceptions;

use DomainException;
use Modules\SmsMarketing\Domain\ValueObjects\SmCampaignStatus;

class InvalidSmCampaignTransition extends DomainException
{
    public static function from(SmCampaignStatus $from, SmCampaignStatus $to): self
    {
        return new self("Cannot transition SMS campaign from [{$from->value}] to [{$to->value}].");
    }
}
