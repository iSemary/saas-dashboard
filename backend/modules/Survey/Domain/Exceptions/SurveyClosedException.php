<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Exceptions;

use RuntimeException;

class SurveyClosedException extends RuntimeException
{
    public function __construct(int $surveyId)
    {
        parent::__construct(
            "Survey {$surveyId} is closed and no longer accepting responses",
            410
        );
    }
}
