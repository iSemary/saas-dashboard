<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\ValueObjects;

enum LeadSource: string
{
    case WEBSITE = 'website';
    case PHONE = 'phone';
    case EMAIL = 'email';
    case SOCIAL = 'social';
    case REFERRAL = 'referral';
    case ADVERTISEMENT = 'advertisement';
    case TRADE_SHOW = 'trade_show';
    case PARTNER = 'partner';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::WEBSITE => 'Website',
            self::PHONE => 'Phone',
            self::EMAIL => 'Email',
            self::SOCIAL => 'Social Media',
            self::REFERRAL => 'Referral',
            self::ADVERTISEMENT => 'Advertisement',
            self::TRADE_SHOW => 'Trade Show',
            self::PARTNER => 'Partner',
            self::OTHER => 'Other',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::WEBSITE => 'globe',
            self::PHONE => 'phone',
            self::EMAIL => 'mail',
            self::SOCIAL => 'share-2',
            self::REFERRAL => 'users',
            self::ADVERTISEMENT => 'speaker',
            self::TRADE_SHOW => 'calendar',
            self::PARTNER => 'handshake',
            self::OTHER => 'help-circle',
        };
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
        ], self::cases());
    }
}
