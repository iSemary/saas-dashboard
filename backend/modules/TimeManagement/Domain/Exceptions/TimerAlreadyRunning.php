<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Exceptions;

use Exception;

class TimerAlreadyRunning extends Exception
{
    public function __construct()
    {
        parent::__construct('A timer is already running for this user. Stop it before starting a new one.');
    }
}
