<?php

namespace Modules\HR\Domain\ValueObjects;

enum LeaveSession: string
{
    case FIRST_HALF = 'first_half';
    case SECOND_HALF = 'second_half';

    public function label(): string
    {
        return match ($this) {
            self::FIRST_HALF => 'First Half',
            self::SECOND_HALF => 'Second Half',
        };
    }
}
