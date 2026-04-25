<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Exceptions;

use RuntimeException;

class InvalidLeadStatusTransition extends RuntimeException
{
    public function __construct(string $fromStatus, string $toStatus)
    {
        parent::__construct(
            "Cannot transition lead status from '{$fromStatus}' to '{$toStatus}'"
        );
    }
}
