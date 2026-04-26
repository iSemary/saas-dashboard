<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\ValueObjects;

enum ReportStatus: string
{
    case DRAFT       = 'draft';
    case SUBMITTED   = 'submitted';
    case APPROVED    = 'approved';
    case REJECTED    = 'rejected';
    case REIMBURSED  = 'reimbursed';

    public static function canTransitionFrom(string $from, self $to): bool
    {
        return match($from) {
            self::DRAFT->value     => in_array($to, [self::SUBMITTED]),
            self::SUBMITTED->value => in_array($to, [self::APPROVED, self::REJECTED]),
            self::APPROVED->value  => in_array($to, [self::REIMBURSED]),
            self::REJECTED->value  => in_array($to, [self::SUBMITTED]),
            self::REIMBURSED->value => false,
            default                => false,
        };
    }
}
