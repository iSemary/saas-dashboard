<?php

namespace Modules\HR\Domain\ValueObjects;

enum EmploymentStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case TERMINATED = 'terminated';
    case ON_LEAVE = 'on_leave';
    case PROBATION = 'probation';
    case SUSPENDED = 'suspended';

    public static function canTransitionFrom(string $from, self $to): bool
    {
        return match($from) {
            self::ACTIVE->value => in_array($to, [self::INACTIVE, self::TERMINATED, self::ON_LEAVE, self::SUSPENDED]),
            self::INACTIVE->value => in_array($to, [self::ACTIVE, self::TERMINATED]),
            self::ON_LEAVE->value => in_array($to, [self::ACTIVE, self::TERMINATED, self::INACTIVE]),
            self::PROBATION->value => in_array($to, [self::ACTIVE, self::INACTIVE, self::TERMINATED]),
            self::SUSPENDED->value => in_array($to, [self::ACTIVE, self::TERMINATED, self::INACTIVE]),
            default => false,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::TERMINATED => 'Terminated',
            self::ON_LEAVE => 'On Leave',
            self::PROBATION => 'Probation',
            self::SUSPENDED => 'Suspended',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'gray',
            self::TERMINATED => 'red',
            self::ON_LEAVE => 'blue',
            self::PROBATION => 'yellow',
            self::SUSPENDED => 'orange',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE || $this === self::PROBATION;
    }

    public function isTerminated(): bool
    {
        return $this === self::TERMINATED;
    }
}
