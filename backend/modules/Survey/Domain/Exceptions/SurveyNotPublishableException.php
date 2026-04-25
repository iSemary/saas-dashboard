<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Exceptions;

use RuntimeException;

class SurveyNotPublishableException extends RuntimeException
{
    public function __construct(int $surveyId, array $errors)
    {
        $errorString = implode(', ', $errors);
        parent::__construct(
            "Survey {$surveyId} cannot be published: {$errorString}",
            422
        );
    }
}
