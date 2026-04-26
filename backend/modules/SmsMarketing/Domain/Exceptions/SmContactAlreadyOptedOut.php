<?php

namespace Modules\SmsMarketing\Domain\Exceptions;

use DomainException;

class SmContactAlreadyOptedOut extends DomainException
{
    public static function forPhone(string $phone): self
    {
        return new self("Contact [{$phone}] has already opted out.");
    }
}
