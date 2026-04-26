<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\ValueObjects;

enum AccountType: string
{
    case ASSET     = 'asset';
    case LIABILITY = 'liability';
    case EQUITY    = 'equity';
    case INCOME    = 'income';
    case EXPENSE   = 'expense';

    public function label(): string
    {
        return match($this) {
            self::ASSET     => 'Asset',
            self::LIABILITY => 'Liability',
            self::EQUITY    => 'Equity',
            self::INCOME    => 'Income',
            self::EXPENSE   => 'Expense',
        };
    }

    public function isDebitNormal(): bool
    {
        return in_array($this, [self::ASSET, self::EXPENSE]);
    }

    public function isCreditNormal(): bool
    {
        return in_array($this, [self::LIABILITY, self::EQUITY, self::INCOME]);
    }
}
