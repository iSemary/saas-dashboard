<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Exceptions;

use RuntimeException;

class SurveyAlreadyPublishedException extends RuntimeException
{
    public function __construct(int $surveyId)
    {
        parent::__construct(
            "Survey {$surveyId} is already published and cannot be modified",
            422
        );
    }
}
