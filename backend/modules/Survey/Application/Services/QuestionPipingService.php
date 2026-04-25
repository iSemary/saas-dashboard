<?php

declare(strict_types=1);

namespace Modules\Survey\Application\Services;

use Modules\Survey\Domain\Entities\SurveyQuestion;

class QuestionPipingService
{
    /**
     * Resolve piping placeholders like {{q1.value}} in question titles/descriptions.
     *
     * @param string $text The text containing placeholders
     * @param array<int, mixed> $answers Map of question_id => answer_value
     * @param array<int, SurveyQuestion> $questions Map of question_id => Question entity
     */
    public function resolvePiping(string $text, array $answers, array $questions): string
    {
        // Pattern: {{q{question_id}.value}} or {{q{question_id}.label}}
        $pattern = '/\{\{q(\d+)\.(value|label)\}\}/';

        return preg_replace_callback($pattern, function ($matches) use ($answers, $questions) {
            $questionId = (int) $matches[1];
            $property = $matches[2];

            if (!isset($answers[$questionId])) {
                return '[not answered]';
            }

            $answer = $answers[$questionId];

            if ($property === 'label') {
                // For choice questions, return the option label instead of value
                if (isset($questions[$questionId]) && $questions[$questionId]->options) {
                    $option = collect($questions[$questionId]->options)
                        ->firstWhere('value', $answer);
                    return $option ? $option->label : $answer;
                }
            }

            return is_array($answer) ? implode(', ', $answer) : (string) $answer;
        }, $text);
    }

    /**
     * Check if a question has piping placeholders.
     */
    public function hasPiping(string $text): bool
    {
        return preg_match('/\{\{q\d+\.(value|label)\}\}/', $text) === 1;
    }

    /**
     * Extract all question IDs referenced in piping placeholders.
     *
     * @return int[]
     */
    public function extractReferencedQuestionIds(string $text): array
    {
        preg_match_all('/\{\{q(\d+)\.(value|label)\}\}/', $text, $matches);
        return array_map('intval', $matches[1] ?? []);
    }
}
