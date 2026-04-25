<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\QuestionType;

use Modules\Survey\Domain\Entities\SurveyQuestion;
use Modules\Survey\Domain\ValueObjects\QuestionType;

interface QuestionTypeStrategyInterface
{
    /**
     * Check if this strategy supports the given question type.
     */
    public function supports(QuestionType $type): bool;

    /**
     * Validate an answer value for a question.
     *
     * @throws \Modules\Survey\Domain\Exceptions\InvalidAnswerException
     */
    public function validateAnswer(SurveyQuestion $question, mixed $value): void;

    /**
     * Format an answer value for storage.
     */
    public function formatAnswer(mixed $value): mixed;

    /**
     * Get the default configuration for this question type.
     */
    public function getDefaultConfig(): array;

    /**
     * Check if this type requires options.
     */
    public function requiresOptions(): bool;

    /**
     * Check if this type supports scoring.
     */
    public function supportsScoring(): bool;

    /**
     * Calculate score for a given answer.
     */
    public function calculateScore(SurveyQuestion $question, mixed $value): ?int;

    /**
     * Get available operators for branching logic.
     */
    public function getAvailableOperators(): array;

    /**
     * Get display component name for frontend.
     */
    public function getDisplayComponent(): string;
}
