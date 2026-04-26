<?php

namespace Modules\EmailMarketing\Domain\Exceptions;

use DomainException;

class EmContactAlreadyUnsubscribed extends DomainException
{
    public static function forEmail(string $email): self
    {
        return new self("Contact [{$email}] is already unsubscribed.");
    }
}
