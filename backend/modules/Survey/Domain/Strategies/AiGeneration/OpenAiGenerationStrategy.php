<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\AiGeneration;

use Modules\Survey\Domain\ValueObjects\SurveyCategory;
use Modules\Survey\Domain\ValueObjects\QuestionType;

/**
 * Stub implementation for OpenAI-based survey generation.
 * Full implementation requires OpenAI API integration.
 */
class OpenAiGenerationStrategy implements AiGenerationStrategyInterface
{
    public function generateSurvey(string $prompt, SurveyCategory $category, array $options = []): array
    {
        // Stub: Return a basic survey structure based on category
        return [
            'title' => $this->generateTitle($prompt),
            'description' => "Survey generated for: {$prompt}",
            'pages' => [
                ['title' => 'Page 1', 'description' => null, 'order' => 1],
            ],
            'questions' => $this->generateDefaultQuestions($category),
            'settings' => [
                'single_question_mode' => false,
                'show_progress' => true,
            ],
        ];
    }

    public function generateQuestions(string $context, int $count = 5, array $options = []): array
    {
        // Stub: Return generic questions
        $types = [
            QuestionType::TEXT,
            QuestionType::MULTIPLE_CHOICE,
            QuestionType::RATING,
            QuestionType::TEXTAREA,
        ];

        $questions = [];
        for ($i = 0; $i < $count; $i++) {
            $type = $types[$i % count($types)];
            $questions[] = [
                'type' => $type->value,
                'title' => "Question " . ($i + 1) . " about {$context}",
                'description' => null,
                'is_required' => $i < 2,
                'config' => $type->getDefaultConfig(),
                'options' => $type->requiresOptions() ? [
                    ['label' => 'Option A', 'value' => 'a', 'order' => 1],
                    ['label' => 'Option B', 'value' => 'b', 'order' => 2],
                    ['label' => 'Option C', 'value' => 'c', 'order' => 3],
                ] : null,
            ];
        }

        return $questions;
    }

    public function improveQuestion(array $questionData, string $feedback = ''): array
    {
        // Stub: Add suggestions
        return [
            'title' => $questionData['title'] ?? 'Untitled Question',
            'description' => $questionData['description'] ?? null,
            'suggestions' => [
                'Consider making the question more specific',
                'Add a description to provide context',
                'Consider whether this should be required',
            ],
        ];
    }

    public function analyzeResponses(array $responses, array $questions): array
    {
        // Stub: Basic analysis
        $total = count($responses);
        $completed = count(array_filter($responses, fn($r) => ($r['status'] ?? null) === 'completed'));

        return [
            'summary' => "Analyzed {$total} responses, {$completed} completed",
            'key_findings' => [
                'Response completion rate: ' . ($total > 0 ? round(($completed / $total) * 100, 1) : 0) . '%',
            ],
            'recommendations' => [
                'Consider following up with partial respondents',
            ],
            'sentiment_analysis' => [
                'overall' => 'neutral',
                'confidence' => 0.5,
            ],
        ];
    }

    public function generateMetadata(string $context): array
    {
        return [
            'title' => $this->generateTitle($context),
            'description' => "A survey about {$context}",
        ];
    }

    public function isAvailable(): bool
    {
        // Check if OpenAI API key is configured
        return !empty(config('services.openai.key'));
    }

    public function getProvider(): string
    {
        return 'openai';
    }

    private function generateTitle(string $prompt): string
    {
        $prompt = trim($prompt);
        $words = explode(' ', $prompt);
        $titleWords = array_slice($words, 0, 6);
        $title = implode(' ', $titleWords);

        return ucfirst($title) . ' Survey';
    }

    private function generateDefaultQuestions(SurveyCategory $category): array
    {
        return match($category) {
            SurveyCategory::NPS => [
                [
                    'type' => QuestionType::NPS->value,
                    'title' => 'How likely are you to recommend us to a friend or colleague?',
                    'description' => null,
                    'is_required' => true,
                    'config' => QuestionType::NPS->getDefaultConfig(),
                    'options' => null,
                ],
            ],
            SurveyCategory::CSAT => [
                [
                    'type' => QuestionType::RATING->value,
                    'title' => 'How satisfied are you with our service?',
                    'description' => null,
                    'is_required' => true,
                    'config' => ['max_rating' => 5, 'symbol' => 'star'],
                    'options' => null,
                ],
            ],
            default => [
                [
                    'type' => QuestionType::TEXT->value,
                    'title' => 'What is your feedback?',
                    'description' => null,
                    'is_required' => true,
                    'config' => QuestionType::TEXT->getDefaultConfig(),
                    'options' => null,
                ],
            ],
        };
    }
}
