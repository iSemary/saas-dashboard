<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\ValueObjects;

enum ActivityType: string
{
    case CALL = 'call';
    case EMAIL = 'email';
    case MEETING = 'meeting';
    case TASK = 'task';
    case NOTE = 'note';
    case SMS = 'sms';
    case DEMO = 'demo';
    case SITE_VISIT = 'site_visit';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CALL => 'Call',
            self::EMAIL => 'Email',
            self::MEETING => 'Meeting',
            self::TASK => 'Task',
            self::NOTE => 'Note',
            self::SMS => 'SMS',
            self::DEMO => 'Demo',
            self::SITE_VISIT => 'Site Visit',
            self::OTHER => 'Other',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CALL => 'phone',
            self::EMAIL => 'mail',
            self::MEETING => 'users',
            self::TASK => 'check-square',
            self::NOTE => 'file-text',
            self::SMS => 'message-circle',
            self::DEMO => 'play-circle',
            self::SITE_VISIT => 'map-pin',
            self::OTHER => 'help-circle',
        };
    }

    public function requiresOutcome(): bool
    {
        return in_array($this, [self::CALL, self::EMAIL, self::MEETING, self::DEMO], true);
    }

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::OTHER;
    }

    public static function all(): array
    {
        return array_map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'icon' => $case->icon(),
            'requires_outcome' => $case->requiresOutcome(),
        ], self::cases());
    }
}
