<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Scoring;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyResponse;
use Modules\Survey\Domain\ValueObjects\QuestionType;
use Modules\Survey\Infrastructure\Persistence\SurveyQuestionRepositoryInterface;

class DefaultScoringStrategy implements ScoringStrategyInterface
{
    public function __construct(
        private SurveyQuestionRepositoryInterface $questionRepository
    ) {}

    public function calculateScore(SurveyResponse $response): int
    {
        $totalScore = 0;

        foreach ($response->answers as $answer) {
            $answerScore = $this->calculateAnswerScore($answer);
            if ($answerScore !== null) {
                $totalScore += $answerScore;
            }
        }

        return $totalScore;
    }

    public function calculateMaxScore(Survey $survey): int
    {
        $questions = $this->questionRepository->getScorableQuestions($survey->id);
        $maxScore = 0;

        foreach ($questions as $question) {
            $questionMax = $this->calculateQuestionMaxScore($question);
            if ($questionMax !== null) {
                $maxScore += $questionMax;
            }
        }

        return $maxScore;
    }

    public function determinePass(SurveyResponse $response, Survey $survey): bool
    {
        $settings = $survey->settings ?? [];
        $passingScore = $settings['passing_score'] ?? 0;
        $passingType = $settings['passing_type'] ?? 'percentage';

        $actualScore = $this->calculateScore($response);

        if ($passingType === 'percentage') {
            $maxScore = $this->calculateMaxScore($survey);
            if ($maxScore === 0) {
                return true;
            }
            $percentage = ($actualScore / $maxScore) * 100;
            return $percentage >= $passingScore;
        }

        return $actualScore >= $passingScore;
    }

    public function getScoreBreakdown(SurveyResponse $response): array
    {
        $breakdown = [];

        foreach ($response->answers as $answer) {
            $question = $answer->question;
            $type = QuestionType::fromString($question->type);

            if (!$type->supportsScoring()) {
                continue;
            }

            $score = $this->calculateAnswerScore($answer);
            $maxScore = $this->calculateQuestionMaxScore($question);

            $breakdown[] = [
                'question_id' => $question->id,
                'question_title' => $question->title,
                'score' => $score ?? 0,
                'max_score' => $maxScore ?? 0,
                'correct' => $maxScore > 0 ? ($score === $maxScore) : null,
                'type' => $question->type,
            ];
        }

        return $breakdown;
    }

    public function calculatePercentile(SurveyResponse $response): ?float
    {
        $surveyId = $response->survey_id;
        $thisScore = $this->calculateScore($response);

        // Count total completed responses
        $totalResponses = SurveyResponse::where('survey_id', $surveyId)
            ->where('status', 'completed')
            ->count();

        if ($totalResponses === 0) {
            return null;
        }

        // Count responses with lower scores
        $lowerScores = SurveyResponse::where('survey_id', $surveyId)
            ->where('status', 'completed')
            ->where('score', '<', $thisScore)
            ->count();

        // Count responses with equal scores
        $equalScores = SurveyResponse::where('survey_id', $surveyId)
            ->where('status', 'completed')
            ->where('score', $thisScore)
            ->count();

        // Use standard percentile formula: (lower + equal/2) / total * 100
        $percentile = (($lowerScores + ($equalScores / 2)) / $totalResponses) * 100;

        return round($percentile, 2);
    }

    private function calculateAnswerScore($answer): ?int
    {
        $question = $answer->question;
        $type = QuestionType::fromString($question->type);

        if (!$type->supportsScoring()) {
            return null;
        }

        // Check for computed_score stored in answer
        if ($answer->computed_score !== null) {
            return $answer->computed_score;
        }

        return match($type) {
            QuestionType::MULTIPLE_CHOICE, QuestionType::DROPDOWN => $this->scoreSingleChoice($question, $answer),
            QuestionType::CHECKBOX => $this->scoreMultipleChoice($question, $answer),
            QuestionType::RATING => $this->scoreRating($question, $answer),
            QuestionType::NPS => $this->scoreNps($question, $answer),
            QuestionType::YES_NO => $this->scoreYesNo($question, $answer),
            QuestionType::LIKERT_SCALE => $this->scoreLikert($question, $answer),
            default => null,
        };
    }

    private function scoreSingleChoice($question, $answer): ?int
    {
        $selectedOptions = $this->getSelectedOptionIds($answer);
        if (empty($selectedOptions)) {
            return null;
        }

        $option = $question->options()->where('id', $selectedOptions[0])->first();
        return $option?->point_value ?? 0;
    }

    private function scoreMultipleChoice($question, $answer): ?int
    {
        $selectedOptions = $this->getSelectedOptionIds($answer);
        if (empty($selectedOptions)) {
            return null;
        }

        $totalPoints = 0;
        foreach ($selectedOptions as $optionId) {
            $option = $question->options()->where('id', $optionId)->first();
            if ($option) {
                $totalPoints += $option->point_value ?? 0;
            }
        }

        return $totalPoints;
    }

    private function scoreRating($question, $answer): ?int
    {
        $rating = $answer->rating_value;
        if ($rating === null) {
            return null;
        }

        $config = $question->config ?? [];
        $maxRating = $config['max_rating'] ?? 5;

        // Score is proportional to rating (out of max_rating points)
        return $rating;
    }

    private function scoreNps($question, $answer): ?int
    {
        $score = $answer->rating_value;
        if ($score === null) {
            return null;
        }

        // NPS score is the value itself (0-10)
        return $score;
    }

    private function scoreYesNo($question, $answer): ?int
    {
        $value = $answer->value;
        if ($value === null) {
            return null;
        }

        $correctAnswer = $question->correct_answer ?? null;
        if ($correctAnswer === null) {
            // No correct answer defined, score 0
            return 0;
        }

        $isCorrect = strtolower($value) === strtolower($correctAnswer);
        return $isCorrect ? 1 : 0;
    }

    private function scoreLikert($question, $answer): ?int
    {
        $rating = $answer->rating_value;
        if ($rating === null) {
            return null;
        }

        // Likert score is the rating value
        return $rating;
    }

    private function calculateQuestionMaxScore($question): ?int
    {
        $type = QuestionType::fromString($question->type);

        if (!$type->supportsScoring()) {
            return null;
        }

        return match($type) {
            QuestionType::MULTIPLE_CHOICE, QuestionType::DROPDOWN, QuestionType::YES_NO => 1,
            QuestionType::CHECKBOX => $question->options()->sum('point_value') ?: 1,
            QuestionType::RATING => $question->config['max_rating'] ?? 5,
            QuestionType::NPS => 10,
            QuestionType::LIKERT_SCALE => $question->config['points'] ?? 5,
            default => 1,
        };
    }

    private function getSelectedOptionIds($answer): array
    {
        $options = $answer->selected_options;

        if (is_string($options)) {
            $options = json_decode($options, true);
        }

        if (!is_array($options)) {
            return [];
        }

        return array_column($options, 'option_id');
    }
}
