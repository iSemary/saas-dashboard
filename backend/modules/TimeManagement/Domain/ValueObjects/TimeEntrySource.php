<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\ValueObjects;

enum TimeEntrySource: string
{
    case Manual = 'manual';
    case Timer = 'timer';
    case Calendar = 'calendar';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Manual',
            self::Timer => 'Timer',
            self::Calendar => 'Calendar',
        };
    }

    public static function fromString(string $value): self
    {
        return self::from($value);
    }
}
