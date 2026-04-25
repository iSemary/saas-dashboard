<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\ValueObjects;

enum SurveyStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case CLOSED = 'closed';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::ACTIVE => 'Active',
            self::PAUSED => 'Paused',
            self::CLOSED => 'Closed',
            self::ARCHIVED => 'Archived',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::ACTIVE => 'green',
            self::PAUSED => 'yellow',
            self::CLOSED => 'red',
            self::ARCHIVED => 'purple',
        };
    }

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::DRAFT;
    }

    public static function canTransitionFrom(SurveyStatus $from, SurveyStatus $to): bool
    {
        $transitions = [
            self::DRAFT->value => [self::ACTIVE->value, self::ARCHIVED->value],
            self::ACTIVE->value => [self::PAUSED->value, self::CLOSED->value, self::ARCHIVED->value],
            self::PAUSED->value => [self::ACTIVE->value, self::CLOSED->value],
            self::CLOSED->value => [self::ACTIVE->value, self::ARCHIVED->value],
            self::ARCHIVED->value => [],
        ];

        return in_array($to->value, $transitions[$from->value] ?? [], true);
    }
}
