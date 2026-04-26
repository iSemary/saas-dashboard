<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Exceptions;

use Exception;

class InvalidTaskStatusTransition extends Exception
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Cannot transition task status from [{$from}] to [{$to}].");
    }
}
