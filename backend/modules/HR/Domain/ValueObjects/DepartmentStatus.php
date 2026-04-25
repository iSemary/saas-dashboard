<?php

namespace Modules\HR\Domain\ValueObjects;

enum DepartmentStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public static function canTransitionFrom(string $from, self $to): bool
    {
        return match($from) {
            self::ACTIVE->value => in_array($to, [self::INACTIVE]),
            self::INACTIVE->value => in_array($to, [self::ACTIVE]),
            default => false,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'gray',
        };
    }
}
