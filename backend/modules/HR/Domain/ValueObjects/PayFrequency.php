<?php

namespace Modules\HR\Domain\ValueObjects;

enum PayFrequency: string
{
    case WEEKLY = 'weekly';
    case BIWEEKLY = 'biweekly';
    case SEMI_MONTHLY = 'semi_monthly';
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case ANNUALLY = 'annually';

    public function label(): string
    {
        return match($this) {
            self::WEEKLY => 'Weekly',
            self::BIWEEKLY => 'Bi-Weekly',
            self::SEMI_MONTHLY => 'Semi-Monthly',
            self::MONTHLY => 'Monthly',
            self::QUARTERLY => 'Quarterly',
            self::ANNUALLY => 'Annually',
        };
    }

    public function paysPerYear(): int
    {
        return match($this) {
            self::WEEKLY => 52,
            self::BIWEEKLY => 26,
            self::SEMI_MONTHLY => 24,
            self::MONTHLY => 12,
            self::QUARTERLY => 4,
            self::ANNUALLY => 1,
        };
    }
}
