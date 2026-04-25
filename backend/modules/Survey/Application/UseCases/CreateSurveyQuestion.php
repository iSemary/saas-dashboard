<?php

declare(strict_types=1);

namespace Modules\Survey\Application\UseCases;

use Modules\Survey\Application\DTOs\CreateSurveyQuestionData;
use Modules\Survey\Domain\Entities\SurveyQuestion;
use Modules\Survey\Infrastructure\Persistence\SurveyQuestionRepositoryInterface;
use Modules\Survey\Infrastructure\Persistence\SurveyPageRepositoryInterface;
use Modules\Survey\Domain\ValueObjects\QuestionType;

class CreateSurveyQuestion
{
    public function __construct(
        private SurveyQuestionRepositoryInterface $questionRepository,
        private SurveyPageRepositoryInterface $pageRepository,
    ) {}

    public function execute(CreateSurveyQuestionData $data): SurveyQuestion
    {
        // Validate page exists and belongs to survey
        $page = $this->pageRepository->findOrFail($data->pageId);
        if ($page->survey_id !== $data->surveyId) {
            throw new \InvalidArgumentException('Page does not belong to survey');
        }

        // Get next order if not specified
        $order = $this->questionRepository->getNextQuestionOrder($data->pageId);

        $questionData = $data->toArray();
        $questionData['order'] = $order;

        // Create question
        $question = $this->questionRepository->create($questionData);

        // Create options if provided
        if (!empty($data->options)) {
            foreach ($data->options as $index => $optionData) {
                $question->addOption([
                    'label' => $optionData['label'],
                    'value' => $optionData['value'] ?? $optionData['label'],
                    'order' => $index + 1,
                    'image_url' => $optionData['image_url'] ?? null,
                    'is_other' => $optionData['is_other'] ?? false,
                    'point_value' => $optionData['point_value'] ?? 0,
                ]);
            }
        }

        return $question->fresh();
    }
}
