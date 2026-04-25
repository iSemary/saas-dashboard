<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Branching;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyQuestion;
use Modules\Survey\Domain\Entities\SurveyResponse;

interface BranchingStrategyInterface
{
    /**
     * Evaluate branching conditions and determine the action to take.
     *
     * @return array{action: string, target_id: ?int} action: show, hide, skip_to_page, skip_to_question
     */
    public function evaluate(
        Survey $survey,
        SurveyQuestion $currentQuestion,
        SurveyResponse $response,
        array $branchingConfig
    ): array;

    /**
     * Check if any branching rules apply to a question for the given response.
     */
    public function shouldShowQuestion(
        SurveyQuestion $question,
        SurveyResponse $response
    ): bool;

    /**
     * Get the next question to display based on branching rules.
     *
     * @return SurveyQuestion|null null if end of survey
     */
    public function getNextQuestion(
        Survey $survey,
        SurveyQuestion $currentQuestion,
        SurveyResponse $response
    ): ?SurveyQuestion;

    /**
     * Evaluate a single condition against an answer.
     */
    public function evaluateCondition(
        SurveyQuestion $targetQuestion,
        mixed $answerValue,
        array $condition
    ): bool;
}
