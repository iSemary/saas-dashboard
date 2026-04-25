<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\AiGeneration;

use Modules\Survey\Domain\ValueObjects\SurveyCategory;

interface AiGenerationStrategyInterface
{
    /**
     * Generate a complete survey from a prompt.
     *
     * @return array{
     *     title: string,
     *     description: string,
     *     pages: array,
     *     questions: array,
     *     settings: array
     * }
     */
    public function generateSurvey(string $prompt, SurveyCategory $category, array $options = []): array;

    /**
     * Generate questions for an existing survey.
     *
     * @return array<int, array{
     *     type: string,
     *     title: string,
     *     description: string|null,
     *     is_required: bool,
     *     config: array,
     *     options: array|null
     * }>
     */
    public function generateQuestions(string $context, int $count = 5, array $options = []): array;

    /**
     * Improve/refine a question.
     *
     * @return array{
     *     title: string,
     *     description: string|null,
     *     suggestions: array<string>
     * }
     */
    public function improveQuestion(array $questionData, string $feedback = ''): array;

    /**
     * Analyze survey responses and generate insights.
     *
     * @return array{
     *     summary: string,
     *     key_findings: array<string>,
     *     recommendations: array<string>,
     *     sentiment_analysis: array
     * }
     */
    public function analyzeResponses(array $responses, array $questions): array;

    /**
     * Generate a survey title and description from context.
     *
     * @return array{title: string, description: string}
     */
    public function generateMetadata(string $context): array;

    /**
     * Check if AI generation is available/configured.
     */
    public function isAvailable(): bool;

    /**
     * Get the provider name.
     */
    public function getProvider(): string;
}
