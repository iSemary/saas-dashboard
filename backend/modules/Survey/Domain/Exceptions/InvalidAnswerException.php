<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Exceptions;

use RuntimeException;

class InvalidAnswerException extends RuntimeException
{
    public function __construct(string $questionTitle, string $reason)
    {
        parent::__construct(
            "Invalid answer for '{$questionTitle}': {$reason}",
            422
        );
    }
}
