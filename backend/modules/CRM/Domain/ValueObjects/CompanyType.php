<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\ValueObjects;

enum CompanyType: string
{
    case CUSTOMER = 'customer';
    case PROSPECT = 'prospect';
    case PARTNER = 'partner';
    case VENDOR = 'vendor';
    case COMPETITOR = 'competitor';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CUSTOMER => 'Customer',
            self::PROSPECT => 'Prospect',
            self::PARTNER => 'Partner',
            self::VENDOR => 'Vendor',
            self::COMPETITOR => 'Competitor',
            self::OTHER => 'Other',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CUSTOMER => 'green',
            self::PROSPECT => 'blue',
            self::PARTNER => 'purple',
            self::VENDOR => 'orange',
            self::COMPETITOR => 'red',
            self::OTHER => 'gray',
        };
    }

    public function canHaveParent(): bool
    {
        return in_array($this, [self::CUSTOMER, self::PROSPECT], true);
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
            'color' => $case->color(),
            'can_have_parent' => $case->canHaveParent(),
        ], self::cases());
    }
}
