<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\ValueObjects;

enum ActivityStatus: string
{
    case PLANNED = 'planned';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case OVERDUE = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::PLANNED => 'Planned',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::OVERDUE => 'Overdue',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PLANNED => 'blue',
            self::IN_PROGRESS => 'yellow',
            self::COMPLETED => 'green',
            self::CANCELLED => 'gray',
            self::OVERDUE => 'red',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::COMPLETED, self::CANCELLED], true);
    }

    public function canTransitionTo(self $to): bool
    {
        if ($this->isTerminal()) {
            return false;
        }

        return true;
    }

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::PLANNED;
    }

    public static function all(): array
    {
        return array_map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'color' => $case->color(),
            'is_terminal' => $case->isTerminal(),
        ], self::cases());
    }
}
