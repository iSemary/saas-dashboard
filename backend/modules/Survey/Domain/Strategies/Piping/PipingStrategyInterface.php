<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Piping;

use Modules\Survey\Domain\Entities\SurveyResponse;

interface PipingStrategyInterface
{
    /**
     * Resolve piping placeholders in text.
     *
     * Placeholders: {{q1.value}}, {{q1.label}}, {{q1.option}}, {{response.email}}, etc.
     *
     * @param string $text The text containing placeholders
     * @param SurveyResponse $response The response to get values from
     * @return string The text with placeholders resolved
     */
    public function resolve(string $text, SurveyResponse $response): string;

    /**
     * Check if text contains any piping placeholders.
     */
    public function hasPlaceholders(string $text): bool;

    /**
     * Extract all unique placeholder keys from text.
     *
     * @return array<int, string>
     */
    public function extractPlaceholders(string $text): array;

    /**
     * Get the value for a specific placeholder key.
     *
     * @param string $key Format: "q{question_id}.{property}" or "response.{property}"
     * @param SurveyResponse $response
     * @return string|null
     */
    public function getValue(string $key, SurveyResponse $response): ?string;

    /**
     * Get available placeholder patterns.
     *
     * @return array<string, array{description: string, example: string}>
     */
    public function getAvailablePatterns(): array;
}
