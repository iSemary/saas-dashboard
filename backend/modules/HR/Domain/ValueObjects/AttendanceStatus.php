<?php

namespace Modules\HR\Domain\ValueObjects;

enum AttendanceStatus: string
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case LATE = 'late';
    case HALF_DAY = 'half_day';
    case LEAVE = 'leave';
    case HOLIDAY = 'holiday';
    case WEEK_OFF = 'week_off';

    public function label(): string
    {
        return match ($this) {
            self::PRESENT => 'Present',
            self::ABSENT => 'Absent',
            self::LATE => 'Late',
            self::HALF_DAY => 'Half Day',
            self::LEAVE => 'Leave',
            self::HOLIDAY => 'Holiday',
            self::WEEK_OFF => 'Week Off',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PRESENT => 'green',
            self::ABSENT => 'red',
            self::LATE => 'orange',
            self::HALF_DAY => 'yellow',
            self::LEAVE => 'blue',
            self::HOLIDAY => 'purple',
            self::WEEK_OFF => 'gray',
        };
    }
}
