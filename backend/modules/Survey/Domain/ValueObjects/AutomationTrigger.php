<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\ValueObjects;

enum AutomationTrigger: string
{
    case RESPONSE_CREATED = 'response_created';
    case RESPONSE_COMPLETED = 'response_completed';
    case QUESTION_ANSWERED = 'question_answered';
    case SURVEY_CLOSED = 'survey_closed';
    case SCORE_REACHED = 'score_reached';

    public static function fromString(string $value): self
    {
        return match($value) {
            'response_created' => self::RESPONSE_CREATED,
            'response_completed' => self::RESPONSE_COMPLETED,
            'question_answered' => self::QUESTION_ANSWERED,
            'survey_closed' => self::SURVEY_CLOSED,
            'score_reached' => self::SCORE_REACHED,
            default => self::RESPONSE_CREATED,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::RESPONSE_CREATED => 'Response Created',
            self::RESPONSE_COMPLETED => 'Response Completed',
            self::QUESTION_ANSWERED => 'Question Answered',
            self::SURVEY_CLOSED => 'Survey Closed',
            self::SCORE_REACHED => 'Score Reached',
        };
    }

    public function requiresConditions(): bool
    {
        return in_array($this, [self::QUESTION_ANSWERED, self::SCORE_REACHED], true);
    }

    public function availableForQuizMode(): bool
    {
        return $this !== self::SCORE_REACHED;
    }
}
