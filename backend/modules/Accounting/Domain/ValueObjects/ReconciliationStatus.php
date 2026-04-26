<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\ValueObjects;

enum ReconciliationStatus: string
{
    case PENDING    = 'pending';
    case MATCHED    = 'matched';
    case UNMATCHED  = 'unmatched';
    case EXCLUDED   = 'excluded';

    public function label(): string
    {
        return match($this) {
            self::PENDING   => 'Pending',
            self::MATCHED   => 'Matched',
            self::UNMATCHED => 'Unmatched',
            self::EXCLUDED  => 'Excluded',
        };
    }
}
