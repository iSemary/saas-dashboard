<?php

namespace Modules\HR\Domain\ValueObjects;

enum EmploymentType: string
{
    case FULL_TIME = 'full_time';
    case PART_TIME = 'part_time';
    case CONTRACT = 'contract';
    case INTERN = 'intern';
    case FREELANCE = 'freelance';
    case CONSULTANT = 'consultant';

    public function label(): string
    {
        return match($this) {
            self::FULL_TIME => 'Full Time',
            self::PART_TIME => 'Part Time',
            self::CONTRACT => 'Contract',
            self::INTERN => 'Intern',
            self::FREELANCE => 'Freelance',
            self::CONSULTANT => 'Consultant',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::FULL_TIME => 'blue',
            self::PART_TIME => 'green',
            self::CONTRACT => 'orange',
            self::INTERN => 'purple',
            self::FREELANCE => 'pink',
            self::CONSULTANT => 'yellow',
        };
    }
}
