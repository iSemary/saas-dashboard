<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\ValueObjects;

enum ExpenseCurrency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case SAR = 'SAR';
    case AED = 'AED';

    public function label(): string
    {
        return match($this) {
            self::USD => 'US Dollar',
            self::EUR => 'Euro',
            self::GBP => 'British Pound',
            self::SAR => 'Saudi Riyal',
            self::AED => 'UAE Dirham',
        };
    }
}
