<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\ValueObjects;

enum ResponseStatus: string
{
    case STARTED = 'started';
    case COMPLETED = 'completed';
    case PARTIAL = 'partial';
    case DISQUALIFIED = 'disqualified';

    public static function fromString(string $value): self
    {
        return match($value) {
            'started' => self::STARTED,
            'completed' => self::COMPLETED,
            'partial' => self::PARTIAL,
            'disqualified' => self::DISQUALIFIED,
            default => self::STARTED,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::STARTED => 'Started',
            self::COMPLETED => 'Completed',
            self::PARTIAL => 'Partial',
            self::DISQUALIFIED => 'Disqualified',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::STARTED => 'warning',
            self::COMPLETED => 'success',
            self::PARTIAL => 'info',
            self::DISQUALIFIED => 'destructive',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::COMPLETED, self::DISQUALIFIED], true);
    }

    public function canResume(): bool
    {
        return $this === self::PARTIAL || $this === self::STARTED;
    }
}
