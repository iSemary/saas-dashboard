<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\ValueObjects;

enum BudgetStatus: string
{
    case DRAFT    = 'draft';
    case ACTIVE   = 'active';
    case ARCHIVED = 'archived';

    public static function canTransitionFrom(string $from, self $to): bool
    {
        return match($from) {
            self::DRAFT->value    => in_array($to, [self::ACTIVE, self::ARCHIVED]),
            self::ACTIVE->value   => in_array($to, [self::ARCHIVED]),
            self::ARCHIVED->value => false,
            default               => false,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::DRAFT    => 'Draft',
            self::ACTIVE   => 'Active',
            self::ARCHIVED => 'Archived',
        };
    }
}
