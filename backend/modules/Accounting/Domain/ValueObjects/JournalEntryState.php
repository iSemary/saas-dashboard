<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\ValueObjects;

enum JournalEntryState: string
{
    case DRAFT     = 'draft';
    case POSTED    = 'posted';
    case CANCELLED = 'cancelled';

    public static function canTransitionFrom(string $from, self $to): bool
    {
        return match($from) {
            self::DRAFT->value     => in_array($to, [self::POSTED, self::CANCELLED]),
            self::POSTED->value    => in_array($to, [self::CANCELLED]),
            self::CANCELLED->value => false,
            default                => false,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::DRAFT     => 'Draft',
            self::POSTED    => 'Posted',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function isEditable(): bool
    {
        return $this === self::DRAFT;
    }
}
