<?php

declare(strict_types=1);

namespace Modules\Survey\Application\UseCases;

use Modules\Survey\Application\DTOs\CreateSurveyData;
use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Infrastructure\Persistence\SurveyRepositoryInterface;

class CreateSurvey
{
    public function __construct(
        private SurveyRepositoryInterface $repository
    ) {}

    public function execute(CreateSurveyData $data, int $userId): Survey
    {
        $surveyData = $data->toArray();
        $surveyData['created_by'] = $userId;
        $surveyData['status'] = 'draft';

        return $this->repository->create($surveyData);
    }
}
