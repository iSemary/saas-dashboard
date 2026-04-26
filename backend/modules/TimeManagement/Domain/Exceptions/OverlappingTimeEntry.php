<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Exceptions;

use Exception;

class OverlappingTimeEntry extends Exception
{
    public function __construct(string $date, string $startTime, string $endTime)
    {
        parent::__construct("Time entry overlaps with existing entry on {$date} ({$startTime} - {$endTime}).");
    }
}
