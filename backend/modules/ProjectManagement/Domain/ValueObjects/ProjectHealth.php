<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\ValueObjects;

enum ProjectHealth: string
{
    case ON_TRACK = 'on_track';
    case AT_RISK = 'at_risk';
    case OFF_TRACK = 'off_track';

    public function label(): string
    {
        return match ($this) {
            self::ON_TRACK => 'On Track',
            self::AT_RISK => 'At Risk',
            self::OFF_TRACK => 'Off Track',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ON_TRACK => 'green',
            self::AT_RISK => 'yellow',
            self::OFF_TRACK => 'red',
        };
    }

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::ON_TRACK;
    }

    public static function fromScore(float $score): self
    {
        return match (true) {
            $score >= 70 => self::ON_TRACK,
            $score >= 40 => self::AT_RISK,
            default => self::OFF_TRACK,
        };
    }
}
