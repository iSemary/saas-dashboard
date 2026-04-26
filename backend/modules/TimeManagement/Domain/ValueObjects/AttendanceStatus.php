<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\ValueObjects;

enum AttendanceStatus: string
{
    case Present = 'present';
    case Absent = 'absent';
    case Late = 'late';
    case HalfDay = 'half_day';
    case Holiday = 'holiday';
    case Leave = 'leave';

    public function label(): string
    {
        return match ($this) {
            self::Present => 'Present',
            self::Absent => 'Absent',
            self::Late => 'Late',
            self::HalfDay => 'Half Day',
            self::Holiday => 'Holiday',
            self::Leave => 'Leave',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Present => '#10B981',
            self::Absent => '#EF4444',
            self::Late => '#F59E0B',
            self::HalfDay => '#3B82F6',
            self::Holiday => '#8B5CF6',
            self::Leave => '#6B7280',
        };
    }

    public static function fromString(string $value): self
    {
        return self::from($value);
    }
}
