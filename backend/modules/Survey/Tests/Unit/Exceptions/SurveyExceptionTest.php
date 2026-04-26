<?php

namespace Modules\Survey\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Modules\Survey\Domain\Exceptions\InvalidAnswerException;
use Modules\Survey\Domain\Exceptions\InvalidQuestionTypeException;
use Modules\Survey\Domain\Exceptions\InvalidSurveyStatusTransition;
use Modules\Survey\Domain\Exceptions\ShareExpiredException;
use Modules\Survey\Domain\Exceptions\SurveyAlreadyPublishedException;
use Modules\Survey\Domain\Exceptions\SurveyClosedException;
use Modules\Survey\Domain\Exceptions\SurveyNotPublishableException;

class SurveyExceptionTest extends TestCase
{
    // ── InvalidAnswerException ────────────────────────────────────

    public function test_invalid_answer_exception_message(): void
    {
        $exception = new InvalidAnswerException('Rating', 'Must be between 1 and 5');
        $this->assertSame("Invalid answer for 'Rating': Must be between 1 and 5", $exception->getMessage());
    }

    public function test_invalid_answer_exception_code_is_422(): void
    {
        $exception = new InvalidAnswerException('Q1', 'Required');
        $this->assertSame(422, $exception->getCode());
    }

    // ── InvalidQuestionTypeException ─────────────────────────────

    public function test_invalid_question_type_exception_message(): void
    {
        $exception = new InvalidQuestionTypeException('matrix');
        $this->assertSame("Invalid question type: 'matrix'", $exception->getMessage());
    }

    public function test_invalid_question_type_exception_code_is_422(): void
    {
        $exception = new InvalidQuestionTypeException('unknown');
        $this->assertSame(422, $exception->getCode());
    }

    // ── InvalidSurveyStatusTransition ─────────────────────────────

    public function test_invalid_survey_status_transition_message(): void
    {
        $exception = new InvalidSurveyStatusTransition('closed', 'active');
        $this->assertSame("Cannot transition survey status from 'closed' to 'active'", $exception->getMessage());
    }

    // ── ShareExpiredException ────────────────────────────────────

    public function test_share_expired_exception_message(): void
    {
        $exception = new ShareExpiredException('abc123');
        $this->assertSame('Survey share link has expired or is no longer valid', $exception->getMessage());
    }

    public function test_share_expired_exception_code_is_410(): void
    {
        $exception = new ShareExpiredException('abc123');
        $this->assertSame(410, $exception->getCode());
    }

    // ── SurveyAlreadyPublishedException ──────────────────────────

    public function test_survey_already_published_exception_message(): void
    {
        $exception = new SurveyAlreadyPublishedException(5);
        $this->assertSame('Survey 5 is already published and cannot be modified', $exception->getMessage());
    }

    // ── SurveyClosedException ─────────────────────────────────────

    public function test_survey_closed_exception_message(): void
    {
        $exception = new SurveyClosedException(10);
        $this->assertSame('Survey 10 is closed and no longer accepting responses', $exception->getMessage());
    }

    public function test_survey_closed_exception_code_is_410(): void
    {
        $exception = new SurveyClosedException(10);
        $this->assertSame(410, $exception->getCode());
    }

    // ── SurveyNotPublishableException ─────────────────────────────

    public function test_survey_not_publishable_exception_message(): void
    {
        $exception = new SurveyNotPublishableException(3, ['No questions', 'Missing title']);
        $this->assertSame('Survey 3 cannot be published: No questions, Missing title', $exception->getMessage());
    }

    public function test_survey_not_publishable_exception_code_is_422(): void
    {
        $exception = new SurveyNotPublishableException(1, ['error']);
        $this->assertSame(422, $exception->getCode());
    }
}
