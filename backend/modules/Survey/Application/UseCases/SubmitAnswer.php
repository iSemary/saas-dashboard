<?php

declare(strict_types=1);

namespace Modules\Survey\Application\UseCases;

use Modules\Survey\Application\DTOs\SubmitAnswerData;
use Modules\Survey\Domain\Entities\SurveyAnswer;
use Modules\Survey\Infrastructure\Persistence\SurveyAnswerRepositoryInterface;
use Modules\Survey\Infrastructure\Persistence\SurveyResponseRepositoryInterface;
use Modules\Survey\Infrastructure\Persistence\SurveyQuestionRepositoryInterface;
use Modules\Survey\Domain\Strategies\QuestionType\QuestionTypeStrategyInterface;
use Modules\Survey\Domain\Events\SurveyQuestionAnswered;

class SubmitAnswer
{
    public function __construct(
        private SurveyAnswerRepositoryInterface $answerRepository,
        private SurveyResponseRepositoryInterface $responseRepository,
        private SurveyQuestionRepositoryInterface $questionRepository,
        private QuestionTypeStrategyInterface $questionTypeStrategy,
    ) {}

    public function execute(SubmitAnswerData $data): SurveyAnswer
    {
        // Validate response exists
        $response = $this->responseRepository->findOrFail($data->responseId);

        // Validate question exists
        $question = $this->questionRepository->findOrFail($data->questionId);

        // Validate answer using strategy
        $value = $data->value;
        if ($data->ratingValue !== null) {
            $value = $data->ratingValue;
        } elseif ($data->selectedOptions !== null) {
            $value = $data->selectedOptions;
        }

        $this->questionTypeStrategy->validateAnswer($question, $value);

        // Check for existing answer
        $existingAnswer = $this->answerRepository->findByResponseAndQuestion(
            $data->responseId,
            $data->questionId
        );

        if ($existingAnswer) {
            // Update existing answer
            $answer = $this->answerRepository->update($existingAnswer->id, $data->toArray());
        } else {
            // Create new answer
            $answer = $this->answerRepository->create($data->toArray());
        }

        // Dispatch event
        event(new SurveyQuestionAnswered($answer, $response));

        return $answer;
    }
}
