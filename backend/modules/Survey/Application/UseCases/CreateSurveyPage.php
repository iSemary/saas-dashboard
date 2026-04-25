<?php

declare(strict_types=1);

namespace Modules\Survey\Application\UseCases;

use Modules\Survey\Application\DTOs\CreateSurveyPageData;
use Modules\Survey\Domain\Entities\SurveyPage;
use Modules\Survey\Infrastructure\Persistence\SurveyPageRepositoryInterface;
use Modules\Survey\Infrastructure\Persistence\SurveyRepositoryInterface;

class CreateSurveyPage
{
    public function __construct(
        private SurveyPageRepositoryInterface $pageRepository,
        private SurveyRepositoryInterface $surveyRepository,
    ) {}

    public function execute(CreateSurveyPageData $data): SurveyPage
    {
        // Validate survey exists
        $this->surveyRepository->findOrFail($data->surveyId);

        // Get next order
        $order = $this->pageRepository->getNextPageOrder($data->surveyId);

        $pageData = $data->toArray();
        $pageData['order'] = $order;

        return $this->pageRepository->create($pageData);
    }
}
