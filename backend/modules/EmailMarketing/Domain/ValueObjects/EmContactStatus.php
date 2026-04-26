<?php

namespace Modules\EmailMarketing\Domain\ValueObjects;

enum EmContactStatus: string
{
    case Active = 'active';
    case Unsubscribed = 'unsubscribed';
    case Bounced = 'bounced';
    case Complained = 'complained';

    public function isReachable(): bool
    {
        return $this === self::Active;
    }
}
