<?php

declare(strict_types=1);

namespace Modules\Survey\Application\UseCases;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Infrastructure\Persistence\SurveyRepositoryInterface;

class PublishSurvey
{
    public function __construct(
        private SurveyRepositoryInterface $repository
    ) {}

    public function execute(int $surveyId): Survey
    {
        $survey = $this->repository->findOrFail($surveyId);
        $survey->publish();
        return $survey->fresh();
    }
}
