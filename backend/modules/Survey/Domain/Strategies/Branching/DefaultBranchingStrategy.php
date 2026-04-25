<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Branching;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyQuestion;
use Modules\Survey\Domain\Entities\SurveyResponse;
use Modules\Survey\Domain\ValueObjects\BranchingOperator;
use Modules\Survey\Infrastructure\Persistence\SurveyQuestionRepositoryInterface;

class DefaultBranchingStrategy implements BranchingStrategyInterface
{
    public function __construct(
        private SurveyQuestionRepositoryInterface $questionRepository
    ) {}

    public function evaluate(
        Survey $survey,
        SurveyQuestion $currentQuestion,
        SurveyResponse $response,
        array $branchingConfig
    ): array {
        $logic = $branchingConfig['logic'] ?? 'AND';
        $conditions = $branchingConfig['conditions'] ?? [];
        $action = $branchingConfig['action'] ?? 'show';
        $targetId = $branchingConfig['target_id'] ?? null;

        if (empty($conditions)) {
            return ['action' => 'show', 'target_id' => null];
        }

        $results = [];
        foreach ($conditions as $condition) {
            $results[] = $this->evaluateSingleCondition($condition, $response);
        }

        $conditionMet = match(strtoupper($logic)) {
            'AND' => !in_array(false, $results, true),
            'OR' => in_array(true, $results, true),
            default => !in_array(false, $results, true),
        };

        if ($conditionMet) {
            return ['action' => $action, 'target_id' => $targetId];
        }

        return ['action' => 'show', 'target_id' => null];
    }

    public function shouldShowQuestion(
        SurveyQuestion $question,
        SurveyResponse $response
    ): bool {
        $branching = $question->branching ?? null;

        if (empty($branching) || empty($branching['conditions'])) {
            return true;
        }

        $result = $this->evaluate(
            $question->survey,
            $question,
            $response,
            $branching
        );

        return $result['action'] !== 'hide';
    }

    public function getNextQuestion(
        Survey $survey,
        SurveyQuestion $currentQuestion,
        SurveyResponse $response
    ): ?SurveyQuestion {
        // Check if current question has skip logic
        $branching = $currentQuestion->branching ?? null;

        if (!empty($branching) && !empty($branching['conditions'])) {
            $result = $this->evaluate($survey, $currentQuestion, $response, $branching);

            if ($result['action'] === 'skip_to_question' && $result['target_id']) {
                return $this->questionRepository->find($result['target_id']);
            }

            if ($result['action'] === 'skip_to_page' && $result['target_id']) {
                // Get first question of target page
                return $this->questionRepository->findFirstOfPage($result['target_id']);
            }
        }

        // Get next question in order
        return $this->questionRepository->findNextQuestion($currentQuestion);
    }

    public function evaluateCondition(
        SurveyQuestion $targetQuestion,
        mixed $answerValue,
        array $condition
    ): bool {
        $operator = BranchingOperator::fromString($condition['operator'] ?? 'eq');
        $expectedValue = $condition['value'] ?? null;

        return $operator->evaluate($answerValue, $expectedValue);
    }

    private function evaluateSingleCondition(array $condition, SurveyResponse $response): bool
    {
        $questionId = $condition['question_id'] ?? null;

        if (!$questionId) {
            return false;
        }

        // Find the answer for this question
        $answer = $response->answers()
            ->where('question_id', $questionId)
            ->first();

        if (!$answer) {
            // If question hasn't been answered, use empty check
            $operator = BranchingOperator::fromString($condition['operator'] ?? 'eq');
            return $operator === BranchingOperator::IS_EMPTY;
        }

        $answerValue = $this->extractAnswerValue($answer);
        $operator = BranchingOperator::fromString($condition['operator'] ?? 'eq');
        $expectedValue = $condition['value'] ?? null;

        return $operator->evaluate($answerValue, $expectedValue);
    }

    private function extractAnswerValue($answer): mixed
    {
        // Priority: rating_value > value > selected_options > matrix_answers
        if ($answer->rating_value !== null) {
            return $answer->rating_value;
        }

        if ($answer->value !== null) {
            return $answer->value;
        }

        if ($answer->selected_options) {
            $options = is_string($answer->selected_options)
                ? json_decode($answer->selected_options, true)
                : $answer->selected_options;
            return array_column($options, 'option_id');
        }

        if ($answer->matrix_answers) {
            $matrix = is_string($answer->matrix_answers)
                ? json_decode($answer->matrix_answers, true)
                : $answer->matrix_answers;
            return $matrix;
        }

        return null;
    }
}
