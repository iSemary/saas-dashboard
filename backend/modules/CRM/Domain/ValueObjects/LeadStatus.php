<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\ValueObjects;

enum LeadStatus: string
{
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case QUALIFIED = 'qualified';
    case UNQUALIFIED = 'unqualified';
    case CONVERTED = 'converted';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::CONTACTED => 'Contacted',
            self::QUALIFIED => 'Qualified',
            self::UNQUALIFIED => 'Unqualified',
            self::CONVERTED => 'Converted',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NEW => 'gray',
            self::CONTACTED => 'blue',
            self::QUALIFIED => 'green',
            self::UNQUALIFIED => 'red',
            self::CONVERTED => 'purple',
        };
    }

    public static function canTransitionFrom(self $from, self $to): bool
    {
        // Define valid transitions
        $transitions = [
            self::NEW->value => [self::CONTACTED, self::UNQUALIFIED, self::CONVERTED],
            self::CONTACTED->value => [self::QUALIFIED, self::UNQUALIFIED, self::CONVERTED],
            self::QUALIFIED->value => [self::UNQUALIFIED, self::CONVERTED],
            self::UNQUALIFIED->value => [self::NEW, self::CONTACTED],
            self::CONVERTED->value => [], // Terminal state
        ];

        return in_array($to, $transitions[$from->value] ?? [], true);
    }

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::NEW;
    }

    public static function all(): array
    {
        return array_map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'color' => $case->color(),
        ], self::cases());
    }
}
