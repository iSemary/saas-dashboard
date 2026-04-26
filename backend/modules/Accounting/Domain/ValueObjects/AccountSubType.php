<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\ValueObjects;

enum AccountSubType: string
{
    case CURRENT_ASSET       = 'current_asset';
    case FIXED_ASSET         = 'fixed_asset';
    case INTANGIBLE_ASSET    = 'intangible_asset';
    case CURRENT_LIABILITY   = 'current_liability';
    case LONG_TERM_LIABILITY = 'long_term_liability';
    case EQUITY              = 'equity';
    case RETAINED_EARNINGS   = 'retained_earnings';
    case OPERATING_INCOME    = 'operating_income';
    case NON_OPERATING_INCOME = 'non_operating_income';
    case OPERATING_EXPENSE   = 'operating_expense';
    case NON_OPERATING_EXPENSE = 'non_operating_expense';

    public function label(): string
    {
        return match($this) {
            self::CURRENT_ASSET        => 'Current Asset',
            self::FIXED_ASSET          => 'Fixed Asset',
            self::INTANGIBLE_ASSET     => 'Intangible Asset',
            self::CURRENT_LIABILITY    => 'Current Liability',
            self::LONG_TERM_LIABILITY  => 'Long-Term Liability',
            self::EQUITY               => 'Equity',
            self::RETAINED_EARNINGS    => 'Retained Earnings',
            self::OPERATING_INCOME     => 'Operating Income',
            self::NON_OPERATING_INCOME => 'Non-Operating Income',
            self::OPERATING_EXPENSE    => 'Operating Expense',
            self::NON_OPERATING_EXPENSE => 'Non-Operating Expense',
        };
    }

    public function forType(): AccountType
    {
        return match($this) {
            self::CURRENT_ASSET, self::FIXED_ASSET, self::INTANGIBLE_ASSET => AccountType::ASSET,
            self::CURRENT_LIABILITY, self::LONG_TERM_LIABILITY             => AccountType::LIABILITY,
            self::EQUITY, self::RETAINED_EARNINGS                          => AccountType::EQUITY,
            self::OPERATING_INCOME, self::NON_OPERATING_INCOME             => AccountType::INCOME,
            self::OPERATING_EXPENSE, self::NON_OPERATING_EXPENSE           => AccountType::EXPENSE,
        };
    }
}
