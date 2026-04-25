<?php

declare(strict_types=1);

namespace Modules\Survey\Application\UseCases;

use Modules\Survey\Domain\Entities\SurveyResponse;
use Modules\Survey\Infrastructure\Persistence\SurveyResponseRepositoryInterface;
use Modules\Survey\Domain\Strategies\Scoring\ScoringStrategyInterface;

class CompleteResponse
{
    public function __construct(
        private SurveyResponseRepositoryInterface $responseRepository,
        private ScoringStrategyInterface $scoringStrategy,
    ) {}

    public function execute(int $responseId): SurveyResponse
    {
        $response = $this->responseRepository->findOrFail($responseId);

        // Calculate time spent
        $timeSpent = $response->calculateTimeSpent();

        // Calculate score if quiz
        $survey = $response->survey;
        if ($survey->isQuiz()) {
            $score = $this->scoringStrategy->calculateScore($response);
            $maxScore = $this->scoringStrategy->calculateMaxScore($survey);
            $passed = $this->scoringStrategy->determinePass($response, $survey);

            $response->updateScore($score, $maxScore, $passed);
        }

        // Complete response
        $response->complete($timeSpent);

        return $response->fresh();
    }
}
