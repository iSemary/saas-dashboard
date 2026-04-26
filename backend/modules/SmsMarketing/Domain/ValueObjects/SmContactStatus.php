<?php

namespace Modules\SmsMarketing\Domain\ValueObjects;

enum SmContactStatus: string
{
    case Active = 'active';
    case OptedOut = 'opted_out';
    case Invalid = 'invalid';

    public function isReachable(): bool
    {
        return $this === self::Active;
    }
}
