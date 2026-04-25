<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Exceptions;

use RuntimeException;

class InvalidSurveyStatusTransition extends RuntimeException
{
    public function __construct(string $fromStatus, string $toStatus)
    {
        parent::__construct(
            "Cannot transition survey status from '{$fromStatus}' to '{$toStatus}'",
            422
        );
    }
}
