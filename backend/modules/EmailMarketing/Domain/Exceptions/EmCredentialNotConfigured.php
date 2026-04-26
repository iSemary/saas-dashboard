<?php

namespace Modules\EmailMarketing\Domain\Exceptions;

use DomainException;

class EmCredentialNotConfigured extends DomainException
{
    public static function noDefault(): self
    {
        return new self('No default email credential is configured for this tenant.');
    }
}
