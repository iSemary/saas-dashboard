<?php

declare(strict_types=1);

namespace Modules\Survey\Application\Services;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyAnswer;
use Modules\Survey\Domain\Entities\SurveyQuestion;
use Modules\Survey\Domain\Entities\SurveyQuestionOption;
use Modules\Survey\Infrastructure\Persistence\SurveyAnswerRepositoryInterface;
use Modules\Survey\Infrastructure\Persistence\SurveyQuestionRepositoryInterface;

class SurveyScoreCalculator
{
    public function __construct(
        private SurveyQuestionRepositoryInterface $questionRepository,
        private SurveyAnswerRepositoryInterface $answerRepository,
    ) {}

    /**
     * Calculate the total score for a survey response.
     *
     * @return array{score: int, max_score: int, passed: bool|null}
     */
    public function calculateScore(int $responseId, Survey $survey): array
    {
        if (!$this->isScoredSurvey($survey)) {
            return ['score' => 0, 'max_score' => 0, 'passed' => null];
        }

        $answers = $this->answerRepository->findByResponse($responseId);
        $questions = $this->getScorableQuestions($survey->id);

        $score = 0;
        $maxScore = 0;

        foreach ($questions as $question) {
            $questionMaxScore = $this->calculateQuestionMaxScore($question);
            $maxScore += $questionMaxScore;

            $answer = $this->findAnswerForQuestion($answers, $question->id);
            if ($answer) {
                $score += $this->calculateAnswerScore($answer, $question);
            }
        }

        $passed = null;
        if ($survey->settings['passing_score'] ?? false) {
            $passingScore = (int) $survey->settings['passing_score'];
            $passed = $score >= $passingScore;
        }

        return [
            'score' => $score,
            'max_score' => $maxScore,
            'passed' => $passed,
        ];
    }

    /**
     * Calculate score for a single answer.
     */
    private function calculateAnswerScore(SurveyAnswer $answer, SurveyQuestion $question): int
    {
        $score = 0;

        if ($question->correct_answer && $answer->value !== null) {
            // Check if answer matches correct answer
            $correctAnswers = is_array($question->correct_answer)
                ? $question->correct_answer
                : [$question->correct_answer];

            if (in_array($answer->value, $correctAnswers, true)) {
                // Full points for correct answer
                $score = $this->calculateQuestionMaxScore($question);
            }
        }

        // Add option point values for partial scoring
        if ($answer->selected_options && $question->options) {
            foreach ($answer->selected_options as $selectedValue) {
                $option = collect($question->options)
                    ->firstWhere('value', $selectedValue);
                if ($option) {
                    $score += (int) ($option->point_value ?? 0);
                }
            }
        }

        // Store computed score on answer for reference
        $answer->computed_score = $score;

        return $score;
    }

    /**
     * Calculate maximum possible score for a question.
     */
    private function calculateQuestionMaxScore(SurveyQuestion $question): int
    {
        if (!$question->options) {
            return $question->correct_answer ? 1 : 0;
        }

        // Sum of all positive point values
        return collect($question->options)
            ->sum(fn ($opt) => max(0, (int) ($opt->point_value ?? 0)));
    }

    /**
     * Get all questions that can be scored.
     *
     * @return SurveyQuestion[]
     */
    private function getScorableQuestions(int $surveyId): array
    {
        return $this->questionRepository->getScorableQuestions($surveyId);
    }

    /**
     * Check if survey has scoring enabled.
     */
    private function isScoredSurvey(Survey $survey): bool
    {
        return $survey->settings['is_scored'] ?? false;
    }

    /**
     * Find answer for a specific question.
     *
     * @param SurveyAnswer[] $answers
     */
    private function findAnswerForQuestion(array $answers, int $questionId): ?SurveyAnswer
    {
        foreach ($answers as $answer) {
            if ($answer->question_id === $questionId) {
                return $answer;
            }
        }
        return null;
    }

    /**
     * Get score breakdown per question.
     *
     * @return array<int, array{question_id: int, title: string, score: int, max_score: int, correct: bool}>
     */
    public function getScoreBreakdown(int $responseId, Survey $survey): array
    {
        if (!$this->isScoredSurvey($survey)) {
            return [];
        }

        $answers = $this->answerRepository->findByResponse($responseId);
        $questions = $this->getScorableQuestions($survey->id);

        $breakdown = [];

        foreach ($questions as $question) {
            $questionMaxScore = $this->calculateQuestionMaxScore($question);
            $answer = $this->findAnswerForQuestion($answers, $question->id);
            $score = $answer ? $this->calculateAnswerScore($answer, $question) : 0;

            $correct = false;
            if ($question->correct_answer && $answer) {
                $correctAnswers = is_array($question->correct_answer)
                    ? $question->correct_answer
                    : [$question->correct_answer];
                $correct = in_array($answer->value, $correctAnswers, true);
            }

            $breakdown[] = [
                'question_id' => $question->id,
                'title' => $question->title,
                'score' => $score,
                'max_score' => $questionMaxScore,
                'correct' => $correct,
            ];
        }

        return $breakdown;
    }
}
