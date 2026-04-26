<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\ValueObjects;

enum BankTransactionType: string
{
    case DEBIT  = 'debit';
    case CREDIT = 'credit';

    public function label(): string
    {
        return match($this) {
            self::DEBIT  => 'Debit',
            self::CREDIT => 'Credit',
        };
    }
}
