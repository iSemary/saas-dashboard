<?php

namespace Modules\HR\Domain\ValueObjects;

enum PositionLevel: string
{
    case EXECUTIVE = 'executive';
    case DIRECTOR = 'director';
    case MANAGER = 'manager';
    case SENIOR = 'senior';
    case MID = 'mid';
    case JUNIOR = 'junior';
    case INTERN = 'intern';
    case CONTRACTOR = 'contractor';

    public function label(): string
    {
        return match($this) {
            self::EXECUTIVE => 'Executive',
            self::DIRECTOR => 'Director',
            self::MANAGER => 'Manager',
            self::SENIOR => 'Senior',
            self::MID => 'Mid-Level',
            self::JUNIOR => 'Junior',
            self::INTERN => 'Intern',
            self::CONTRACTOR => 'Contractor',
        };
    }

    public function order(): int
    {
        return match($this) {
            self::EXECUTIVE => 1,
            self::DIRECTOR => 2,
            self::MANAGER => 3,
            self::SENIOR => 4,
            self::MID => 5,
            self::JUNIOR => 6,
            self::INTERN => 7,
            self::CONTRACTOR => 8,
        };
    }
}
