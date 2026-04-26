<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\ValueObjects;

enum TimeEntryStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted => 'Submitted',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => '#6B7280',
            self::Submitted => '#3B82F6',
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
            self::Draft => $to === self::Submitted,
            self::Submitted => $to === self::Approved || $to === self::Rejected,
            self::Approved => false,
            self::Rejected => $to === self::Draft || $to === self::Submitted,
        };
    }
}
