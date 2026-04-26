<?php

namespace Modules\EmailMarketing\Domain\Exceptions;

use DomainException;
use Modules\EmailMarketing\Domain\ValueObjects\EmCampaignStatus;

class InvalidEmCampaignTransition extends DomainException
{
    public static function from(EmCampaignStatus $from, EmCampaignStatus $to): self
    {
        return new self("Cannot transition email campaign from [{$from->value}] to [{$to->value}].");
    }
}
