<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\ValueObjects;

enum BranchingOperator: string
{
    case EQUALS = 'eq';
    case NOT_EQUALS = 'neq';
    case CONTAINS = 'contains';
    case NOT_CONTAINS = 'not_contains';
    case GREATER_THAN = 'gt';
    case LESS_THAN = 'lt';
    case GREATER_OR_EQUAL = 'gte';
    case LESS_OR_EQUAL = 'lte';
    case IN = 'in';
    case NOT_IN = 'not_in';
    case IS_EMPTY = 'is_empty';
    case IS_NOT_EMPTY = 'is_not_empty';

    public static function fromString(string $value): self
    {
        return match($value) {
            'eq' => self::EQUALS,
            'neq' => self::NOT_EQUALS,
            'contains' => self::CONTAINS,
            'not_contains' => self::NOT_CONTAINS,
            'gt' => self::GREATER_THAN,
            'lt' => self::LESS_THAN,
            'gte' => self::GREATER_OR_EQUAL,
            'lte' => self::LESS_OR_EQUAL,
            'in' => self::IN,
            'not_in' => self::NOT_IN,
            'is_empty' => self::IS_EMPTY,
            'is_not_empty' => self::IS_NOT_EMPTY,
            default => self::EQUALS,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::EQUALS => 'equals',
            self::NOT_EQUALS => 'does not equal',
            self::CONTAINS => 'contains',
            self::NOT_CONTAINS => 'does not contain',
            self::GREATER_THAN => 'is greater than',
            self::LESS_THAN => 'is less than',
            self::GREATER_OR_EQUAL => 'is greater or equal to',
            self::LESS_OR_EQUAL => 'is less or equal to',
            self::IN => 'is one of',
            self::NOT_IN => 'is not one of',
            self::IS_EMPTY => 'is empty',
            self::IS_NOT_EMPTY => 'is not empty',
        };
    }

    public function evaluate(mixed $actual, mixed $expected): bool
    {
        return match($this) {
            self::EQUALS => $actual == $expected,
            self::NOT_EQUALS => $actual != $expected,
            self::CONTAINS => is_string($actual) && is_string($expected) && str_contains($actual, $expected),
            self::NOT_CONTAINS => is_string($actual) && is_string($expected) && !str_contains($actual, $expected),
            self::GREATER_THAN => is_numeric($actual) && is_numeric($expected) && $actual > $expected,
            self::LESS_THAN => is_numeric($actual) && is_numeric($expected) && $actual < $expected,
            self::GREATER_OR_EQUAL => is_numeric($actual) && is_numeric($expected) && $actual >= $expected,
            self::LESS_OR_EQUAL => is_numeric($actual) && is_numeric($expected) && $actual <= $expected,
            self::IN => in_array($actual, is_array($expected) ? $expected : [$expected], true),
            self::NOT_IN => !in_array($actual, is_array($expected) ? $expected : [$expected], true),
            self::IS_EMPTY => empty($actual),
            self::IS_NOT_EMPTY => !empty($actual),
        };
    }

    public function suitableForType(QuestionType $type): bool
    {
        return match($type) {
            QuestionType::TEXT, QuestionType::TEXTAREA, QuestionType::EMAIL, QuestionType::URL => in_array($this, [
                self::EQUALS, self::NOT_EQUALS, self::CONTAINS, self::NOT_CONTAINS,
                self::IS_EMPTY, self::IS_NOT_EMPTY,
            ]),
            QuestionType::NUMBER, QuestionType::RATING, QuestionType::NPS, QuestionType::SLIDER => in_array($this, [
                self::EQUALS, self::NOT_EQUALS, self::GREATER_THAN, self::LESS_THAN,
                self::GREATER_OR_EQUAL, self::LESS_OR_EQUAL, self::IS_EMPTY, self::IS_NOT_EMPTY,
            ]),
            QuestionType::MULTIPLE_CHOICE, QuestionType::DROPDOWN => in_array($this, [
                self::EQUALS, self::NOT_EQUALS, self::IN, self::NOT_IN, self::IS_EMPTY, self::IS_NOT_EMPTY,
            ]),
            QuestionType::CHECKBOX => in_array($this, [
                self::CONTAINS, self::NOT_CONTAINS, self::IN, self::NOT_IN,
                self::IS_EMPTY, self::IS_NOT_EMPTY,
            ]),
            QuestionType::YES_NO => in_array($this, [
                self::EQUALS, self::NOT_EQUALS, self::IS_EMPTY, self::IS_NOT_EMPTY,
            ]),
            default => in_array($this, [
                self::EQUALS, self::NOT_EQUALS, self::IS_EMPTY, self::IS_NOT_EMPTY,
            ]),
        };
    }
}
