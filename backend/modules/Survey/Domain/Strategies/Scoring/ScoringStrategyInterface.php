<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Scoring;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyResponse;

interface ScoringStrategyInterface
{
    /**
     * Calculate the total score for a survey response.
     */
    public function calculateScore(SurveyResponse $response): int;

    /**
     * Calculate the maximum possible score for the survey.
     */
    public function calculateMaxScore(Survey $survey): int;

    /**
     * Determine if the respondent passed the survey (if quiz mode).
     */
    public function determinePass(SurveyResponse $response, Survey $survey): bool;

    /**
     * Get score breakdown by question.
     *
     * @return array<array{question_id: int, question_title: string, score: int, max_score: int, correct: bool|null}>
     */
    public function getScoreBreakdown(SurveyResponse $response): array;

    /**
     * Calculate percentile score compared to other responses.
     */
    public function calculatePercentile(SurveyResponse $response): ?float;
}
