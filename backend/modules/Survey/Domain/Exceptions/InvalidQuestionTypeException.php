<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Exceptions;

use RuntimeException;

class InvalidQuestionTypeException extends RuntimeException
{
    public function __construct(string $type)
    {
        parent::__construct(
            "Invalid question type: '{$type}'",
            422
        );
    }
}
