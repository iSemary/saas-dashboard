<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Piping;

use Modules\Survey\Domain\Entities\SurveyResponse;
use Modules\Survey\Infrastructure\Persistence\SurveyQuestionRepositoryInterface;

class DefaultPipingStrategy implements PipingStrategyInterface
{
    private const PLACEHOLDER_PATTERN = '/\{\{([^{}]+)\}\}/';

    public function __construct(
        private SurveyQuestionRepositoryInterface $questionRepository
    ) {}

    public function resolve(string $text, SurveyResponse $response): string
    {
        return preg_replace_callback(self::PLACEHOLDER_PATTERN, function ($matches) use ($response) {
            $key = trim($matches[1]);
            $value = $this->getValue($key, $response);
            return $value ?? $matches[0]; // Return original if not found
        }, $text);
    }

    public function hasPlaceholders(string $text): bool
    {
        return preg_match(self::PLACEHOLDER_PATTERN, $text) === 1;
    }

    public function extractPlaceholders(string $text): array
    {
        preg_match_all(self::PLACEHOLDER_PATTERN, $text, $matches);
        return array_unique($matches[1] ?? []);
    }

    public function getValue(string $key, SurveyResponse $response): ?string
    {
        $parts = explode('.', $key);

        if (count($parts) < 2) {
            return null;
        }

        $source = $parts[0];
        $property = $parts[1];

        return match($source) {
            'response' => $this->getResponseValue($response, $property),
            default => $this->getQuestionValue($source, $property, $response),
        };
    }

    public function getAvailablePatterns(): array
    {
        return [
            'q{id}.value' => [
                'description' => 'The raw answer value',
                'example' => '{{q1.value}}',
            ],
            'q{id}.label' => [
                'description' => 'The question title',
                'example' => '{{q1.label}}',
            ],
            'q{id}.option' => [
                'description' => 'The selected option label(s)',
                'example' => '{{q1.option}}',
            ],
            'response.email' => [
                'description' => 'Respondent email',
                'example' => '{{response.email}}',
            ],
            'response.name' => [
                'description' => 'Respondent name',
                'example' => '{{response.name}}',
            ],
        ];
    }

    private function getResponseValue(SurveyResponse $response, string $property): ?string
    {
        return match($property) {
            'email' => $response->respondent_email,
            'name' => $response->respondent_name,
            'id' => (string) $response->id,
            'started_at' => $response->started_at?->format('Y-m-d H:i'),
            'completed_at' => $response->completed_at?->format('Y-m-d H:i'),
            default => null,
        };
    }

    private function getQuestionValue(string $questionRef, string $property, SurveyResponse $response): ?string
    {
        // Parse question ID from reference like "q1" or just "1"
        $questionId = is_numeric($questionRef)
            ? (int) $questionRef
            : (int) str_replace('q', '', $questionRef);

        $answer = $response->answers()
            ->where('question_id', $questionId)
            ->first();

        if (!$answer) {
            return null;
        }

        $question = $answer->question;

        return match($property) {
            'value' => $this->formatValue($answer->value),
            'label' => $question?->title,
            'option' => $this->formatSelectedOptions($answer->selected_options, $question),
            'rating' => $answer->rating_value !== null ? (string) $answer->rating_value : null,
            default => null,
        };
    }

    private function formatValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Truncate very long values
        if (strlen($value) > 100) {
            return substr($value, 0, 100) . '...';
        }

        return $value;
    }

    private function formatSelectedOptions($selectedOptions, $question): ?string
    {
        if (empty($selectedOptions)) {
            return null;
        }

        $options = is_string($selectedOptions)
            ? json_decode($selectedOptions, true)
            : $selectedOptions;

        if (!is_array($options)) {
            return null;
        }

        $labels = [];
        foreach ($options as $option) {
            $optionId = $option['option_id'] ?? null;
            $otherText = $option['other_text'] ?? null;

            if ($otherText) {
                $labels[] = $otherText;
            } elseif ($optionId && $question) {
                $opt = $question->options()->where('id', $optionId)->first();
                if ($opt) {
                    $labels[] = $opt->label;
                }
            }
        }

        return implode(', ', $labels);
    }
}
