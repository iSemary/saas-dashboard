<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Exceptions;

use Exception;

class InvalidTimesheetStatusTransition extends Exception
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Cannot transition timesheet status from '{$from}' to '{$to}'.");
    }
}
