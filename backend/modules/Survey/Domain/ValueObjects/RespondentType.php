<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\ValueObjects;

enum RespondentType: string
{
    case ANONYMOUS = 'anonymous';
    case AUTHENTICATED = 'authenticated';
    case EMAIL = 'email';

    public static function fromString(string $value): self
    {
        return match($value) {
            'anonymous' => self::ANONYMOUS,
            'authenticated' => self::AUTHENTICATED,
            'email' => self::EMAIL,
            default => self::ANONYMOUS,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::ANONYMOUS => 'Anonymous',
            self::AUTHENTICATED => 'Authenticated',
            self::EMAIL => 'Email',
        };
    }

    public function requiresAuth(): bool
    {
        return $this === self::AUTHENTICATED;
    }

    public function collectsEmail(): bool
    {
        return in_array($this, [self::EMAIL, self::AUTHENTICATED], true);
    }
}
