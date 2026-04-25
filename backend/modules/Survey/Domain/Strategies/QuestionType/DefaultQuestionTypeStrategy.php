<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\QuestionType;

use Modules\Survey\Domain\Entities\SurveyQuestion;
use Modules\Survey\Domain\ValueObjects\QuestionType;
use Modules\Survey\Domain\ValueObjects\BranchingOperator;
use Modules\Survey\Domain\Exceptions\InvalidAnswerException;

class DefaultQuestionTypeStrategy implements QuestionTypeStrategyInterface
{
    public function supports(QuestionType $type): bool
    {
        return true;
    }

    public function validateAnswer(SurveyQuestion $question, mixed $value): void
    {
        $type = QuestionType::fromString($question->type);
        $config = $question->config ?? [];
        $validation = $question->validation ?? [];

        // Check if required
        if ($question->is_required && ($value === null || $value === '' || $value === [])) {
            throw new InvalidAnswerException($question->title, 'This question is required');
        }

        // Skip validation if empty and not required
        if (!$question->is_required && ($value === null || $value === '' || $value === [])) {
            return;
        }

        // Type-specific validation
        match($type) {
            QuestionType::EMAIL => $this->validateEmail($value, $question->title),
            QuestionType::URL => $this->validateUrl($value, $question->title),
            QuestionType::NUMBER => $this->validateNumber($value, $validation, $question->title),
            QuestionType::TEXT, QuestionType::TEXTAREA => $this->validateText($value, $validation, $question->title),
            QuestionType::RATING => $this->validateRating($value, $config, $question->title),
            QuestionType::NPS => $this->validateNps($value, $question->title),
            QuestionType::PHONE => $this->validatePhone($value, $question->title),
            default => null,
        };
    }

    public function formatAnswer(mixed $value): mixed
    {
        if (is_array($value)) {
            return json_encode($value);
        }
        return $value;
    }

    public function getDefaultConfig(): array
    {
        return [];
    }

    public function requiresOptions(): bool
    {
        return false;
    }

    public function supportsScoring(): bool
    {
        return false;
    }

    public function calculateScore(SurveyQuestion $question, mixed $value): ?int
    {
        return null;
    }

    public function getAvailableOperators(): array
    {
        return [
            BranchingOperator::EQUALS,
            BranchingOperator::NOT_EQUALS,
            BranchingOperator::IS_EMPTY,
            BranchingOperator::IS_NOT_EMPTY,
        ];
    }

    public function getDisplayComponent(): string
    {
        return 'TextQuestion';
    }

    private function validateEmail(mixed $value, string $title): void
    {
        if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidAnswerException($title, 'Please enter a valid email address');
        }
    }

    private function validateUrl(mixed $value, string $title): void
    {
        if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_URL)) {
            throw new InvalidAnswerException($title, 'Please enter a valid URL');
        }
    }

    private function validateNumber(mixed $value, array $validation, string $title): void
    {
        if (!is_numeric($value)) {
            throw new InvalidAnswerException($title, 'Please enter a valid number');
        }

        $num = (float) $value;

        if (isset($validation['min']) && $num < $validation['min']) {
            throw new InvalidAnswerException($title, "Value must be at least {$validation['min']}");
        }

        if (isset($validation['max']) && $num > $validation['max']) {
            throw new InvalidAnswerException($title, "Value must be at most {$validation['max']}");
        }
    }

    private function validateText(mixed $value, array $validation, string $title): void
    {
        if (!is_string($value)) {
            throw new InvalidAnswerException($title, 'Please enter valid text');
        }

        $length = mb_strlen($value);

        if (isset($validation['min_length']) && $length < $validation['min_length']) {
            throw new InvalidAnswerException($title, "Answer must be at least {$validation['min_length']} characters");
        }

        if (isset($validation['max_length']) && $length > $validation['max_length']) {
            throw new InvalidAnswerException($title, "Answer must be at most {$validation['max_length']} characters");
        }

        if (isset($validation['pattern']) && !preg_match('/' . $validation['pattern'] . '/', $value)) {
            throw new InvalidAnswerException($title, 'Answer does not match the required pattern');
        }
    }

    private function validateRating(mixed $value, array $config, string $title): void
    {
        $maxRating = $config['max_rating'] ?? 5;

        if (!is_numeric($value) || $value < 1 || $value > $maxRating) {
            throw new InvalidAnswerException($title, "Rating must be between 1 and {$maxRating}");
        }
    }

    private function validateNps(mixed $value, string $title): void
    {
        if (!is_numeric($value) || $value < 0 || $value > 10) {
            throw new InvalidAnswerException($title, 'NPS must be between 0 and 10');
        }
    }

    private function validatePhone(mixed $value, string $title): void
    {
        if (!is_string($value) || strlen($value) < 5) {
            throw new InvalidAnswerException($title, 'Please enter a valid phone number');
        }
    }
}
