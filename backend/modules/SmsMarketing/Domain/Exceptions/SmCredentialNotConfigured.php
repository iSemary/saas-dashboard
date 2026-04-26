<?php

namespace Modules\SmsMarketing\Domain\Exceptions;

use DomainException;

class SmCredentialNotConfigured extends DomainException
{
    public static function noDefault(): self
    {
        return new self('No default SMS credential is configured for this tenant.');
    }
}
