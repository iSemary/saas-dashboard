<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\ValueObjects;

enum ProjectStatus: string
{
    case PLANNING = 'planning';
    case ACTIVE = 'active';
    case ON_HOLD = 'on_hold';
    case COMPLETED = 'completed';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::PLANNING => 'Planning',
            self::ACTIVE => 'Active',
            self::ON_HOLD => 'On Hold',
            self::COMPLETED => 'Completed',
            self::ARCHIVED => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PLANNING => 'blue',
            self::ACTIVE => 'green',
            self::ON_HOLD => 'yellow',
            self::COMPLETED => 'purple',
            self::ARCHIVED => 'gray',
        };
    }

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::PLANNING;
    }

    public static function canTransitionFrom(ProjectStatus $from, ProjectStatus $to): bool
    {
        $transitions = [
            self::PLANNING->value => [self::ACTIVE->value, self::ARCHIVED->value],
            self::ACTIVE->value => [self::ON_HOLD->value, self::COMPLETED->value, self::ARCHIVED->value],
            self::ON_HOLD->value => [self::ACTIVE->value, self::COMPLETED->value, self::ARCHIVED->value],
            self::COMPLETED->value => [self::ARCHIVED->value],
            self::ARCHIVED->value => [],
        ];

        return in_array($to->value, $transitions[$from->value] ?? [], true);
    }
}
