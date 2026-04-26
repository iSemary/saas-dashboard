<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\ValueObjects;

enum OvertimeRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => '#F59E0B',
            self::Approved => '#10B981',
            self::Rejected => '#EF4444',
        };
    }

    public static function fromString(string $value): self
    {
        return self::from($value);
    }

    public static function canTransitionFrom(self $from, self $to): bool
    {
        return match ($from) {
            self::Pending => $to === self::Approved || $to === self::Rejected,
            self::Approved => false,
            self::Rejected => false,
        };
    }
}
