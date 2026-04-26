<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\ValueObjects;

enum PolicyType: string
{
    case MAX_AMOUNT            = 'max_amount';
    case RECEIPT_REQUIRED      = 'receipt_required';
    case AUTO_APPROVAL         = 'auto_approval';
    case CATEGORY_RESTRICTION  = 'category_restriction';
    case DUPLICATE_CHECK       = 'duplicate_check';

    public function label(): string
    {
        return match($this) {
            self::MAX_AMOUNT           => 'Max Amount',
            self::RECEIPT_REQUIRED     => 'Receipt Required',
            self::AUTO_APPROVAL        => 'Auto Approval',
            self::CATEGORY_RESTRICTION => 'Category Restriction',
            self::DUPLICATE_CHECK      => 'Duplicate Check',
        };
    }
}
