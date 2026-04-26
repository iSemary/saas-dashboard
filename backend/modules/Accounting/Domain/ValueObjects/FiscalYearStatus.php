<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\ValueObjects;

enum FiscalYearStatus: string
{
    case OPEN   = 'open';
    case CLOSED = 'closed';
    case LOCKED = 'locked';

    public static function canTransitionFrom(string $from, self $to): bool
    {
        return match($from) {
            self::OPEN->value   => in_array($to, [self::CLOSED, self::LOCKED]),
            self::CLOSED->value => in_array($to, [self::LOCKED]),
            self::LOCKED->value => false,
            default             => false,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::OPEN   => 'Open',
            self::CLOSED => 'Closed',
            self::LOCKED => 'Locked',
        };
    }
}
