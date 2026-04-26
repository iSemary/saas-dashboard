<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Exceptions;

use Exception;

class CalendarConflictDetected extends Exception
{
    public function __construct(int $conflictCount = 1)
    {
        parent::__construct("Calendar conflict detected. {$conflictCount} conflicting event(s) found.");
    }
}
