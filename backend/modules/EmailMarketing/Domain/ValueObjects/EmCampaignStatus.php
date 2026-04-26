<?php

namespace Modules\EmailMarketing\Domain\ValueObjects;

enum EmCampaignStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Sending = 'sending';
    case Sent = 'sent';
    case Paused = 'paused';
    case Cancelled = 'cancelled';

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Draft => in_array($next, [self::Scheduled, self::Sending, self::Cancelled]),
            self::Scheduled => in_array($next, [self::Sending, self::Cancelled, self::Draft]),
            self::Sending => in_array($next, [self::Sent, self::Paused, self::Cancelled]),
            self::Paused => in_array($next, [self::Sending, self::Cancelled]),
            self::Sent, self::Cancelled => false,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Scheduled => 'Scheduled',
            self::Sending => 'Sending',
            self::Sent => 'Sent',
            self::Paused => 'Paused',
            self::Cancelled => 'Cancelled',
        };
    }
}
