<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\ValueObjects;

enum ExpenseStatus: string
{
    case DRAFT       = 'draft';
    case PENDING     = 'pending';
    case APPROVED    = 'approved';
    case REJECTED    = 'rejected';
    case REIMBURSED  = 'reimbursed';
    case CANCELLED   = 'cancelled';

    public static function canTransitionFrom(string $from, self $to): bool
    {
        return match($from) {
            self::DRAFT->value      => in_array($to, [self::PENDING, self::CANCELLED]),
            self::PENDING->value    => in_array($to, [self::APPROVED, self::REJECTED, self::CANCELLED]),
            self::APPROVED->value   => in_array($to, [self::REIMBURSED, self::CANCELLED]),
            self::REJECTED->value   => in_array($to, [self::PENDING, self::CANCELLED]),
            self::REIMBURSED->value => false,
            self::CANCELLED->value  => false,
            default                 => false,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::DRAFT      => 'Draft',
            self::PENDING    => 'Pending',
            self::APPROVED   => 'Approved',
            self::REJECTED   => 'Rejected',
            self::REIMBURSED => 'Reimbursed',
            self::CANCELLED  => 'Cancelled',
        };
    }
}
