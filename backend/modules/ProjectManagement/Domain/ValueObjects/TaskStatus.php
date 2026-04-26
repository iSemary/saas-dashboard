<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\ValueObjects;

enum TaskStatus: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case IN_REVIEW = 'in_review';
    case DONE = 'done';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::TODO => 'To Do',
            self::IN_PROGRESS => 'In Progress',
            self::IN_REVIEW => 'In Review',
            self::DONE => 'Done',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TODO => 'gray',
            self::IN_PROGRESS => 'blue',
            self::IN_REVIEW => 'yellow',
            self::DONE => 'green',
            self::CANCELLED => 'red',
        };
    }

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::TODO;
    }

    public static function canTransitionFrom(TaskStatus $from, TaskStatus $to): bool
    {
        $transitions = [
            self::TODO->value => [self::IN_PROGRESS->value, self::CANCELLED->value],
            self::IN_PROGRESS->value => [self::IN_REVIEW->value, self::TODO->value, self::CANCELLED->value],
            self::IN_REVIEW->value => [self::DONE->value, self::IN_PROGRESS->value, self::CANCELLED->value],
            self::DONE->value => [self::TODO->value],
            self::CANCELLED->value => [self::TODO->value],
        ];

        return in_array($to->value, $transitions[$from->value] ?? [], true);
    }
}
