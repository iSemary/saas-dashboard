<?php

namespace Modules\Survey\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\Survey\Domain\ValueObjects\SurveyStatus;
use Modules\Survey\Domain\ValueObjects\ResponseStatus;
use Modules\Survey\Domain\ValueObjects\QuestionType;
use Modules\Survey\Domain\ValueObjects\RespondentType;
use Modules\Survey\Domain\ValueObjects\SurveyCategory;
use Modules\Survey\Domain\ValueObjects\ShareChannel;
use Modules\Survey\Domain\ValueObjects\AutomationTrigger;
use Modules\Survey\Domain\ValueObjects\AutomationAction;
use Modules\Survey\Domain\ValueObjects\BranchingOperator;

class SurveyValueObjectTest extends TestCase
{
    // ── SurveyStatus ──────────────────────────────────────────────

    public function test_survey_status_values(): void
    {
        $this->assertSame('draft', SurveyStatus::DRAFT->value);
        $this->assertSame('active', SurveyStatus::ACTIVE->value);
        $this->assertSame('paused', SurveyStatus::PAUSED->value);
        $this->assertSame('closed', SurveyStatus::CLOSED->value);
        $this->assertSame('archived', SurveyStatus::ARCHIVED->value);
    }

    public function test_survey_status_all_cases_covered(): void
    {
        $this->assertCount(5, SurveyStatus::cases());
    }

    // ── ResponseStatus ────────────────────────────────────────────

    public function test_response_status_values(): void
    {
        $this->assertSame('started', ResponseStatus::STARTED->value);
        $this->assertSame('completed', ResponseStatus::COMPLETED->value);
        $this->assertSame('partial', ResponseStatus::PARTIAL->value);
        $this->assertSame('disqualified', ResponseStatus::DISQUALIFIED->value);
    }

    public function test_response_status_is_final(): void
    {
        $this->assertTrue(ResponseStatus::COMPLETED->isFinal());
        $this->assertTrue(ResponseStatus::DISQUALIFIED->isFinal());
        $this->assertFalse(ResponseStatus::STARTED->isFinal());
        $this->assertFalse(ResponseStatus::PARTIAL->isFinal());
    }

    public function test_response_status_can_resume(): void
    {
        $this->assertTrue(ResponseStatus::STARTED->canResume());
        $this->assertTrue(ResponseStatus::PARTIAL->canResume());
        $this->assertFalse(ResponseStatus::COMPLETED->canResume());
    }

    public function test_response_status_from_string(): void
    {
        $this->assertSame(ResponseStatus::COMPLETED, ResponseStatus::fromString('completed'));
        $this->assertSame(ResponseStatus::STARTED, ResponseStatus::fromString('unknown'));
    }

    // ── QuestionType ──────────────────────────────────────────────

    public function test_question_type_all_cases_covered(): void
    {
        $this->assertCount(20, QuestionType::cases());
    }

    public function test_question_type_is_choice_type(): void
    {
        $this->assertTrue(QuestionType::MULTIPLE_CHOICE->isChoiceType());
        $this->assertTrue(QuestionType::CHECKBOX->isChoiceType());
        $this->assertTrue(QuestionType::DROPDOWN->isChoiceType());
        $this->assertFalse(QuestionType::TEXT->isChoiceType());
    }

    public function test_question_type_is_rating_type(): void
    {
        $this->assertTrue(QuestionType::RATING->isRatingType());
        $this->assertTrue(QuestionType::NPS->isRatingType());
        $this->assertFalse(QuestionType::TEXT->isRatingType());
    }

    public function test_question_type_requires_options(): void
    {
        $this->assertTrue(QuestionType::MULTIPLE_CHOICE->requiresOptions());
        $this->assertFalse(QuestionType::TEXT->requiresOptions());
    }

    public function test_question_type_supports_scoring(): void
    {
        $this->assertTrue(QuestionType::MULTIPLE_CHOICE->supportsScoring());
        $this->assertTrue(QuestionType::YES_NO->supportsScoring());
        $this->assertTrue(QuestionType::RATING->supportsScoring());
        $this->assertFalse(QuestionType::TEXT->supportsScoring());
    }

    public function test_question_type_from_string(): void
    {
        $this->assertSame(QuestionType::NPS, QuestionType::fromString('nps'));
        $this->assertSame(QuestionType::TEXT, QuestionType::fromString('unknown'));
    }

    // ── RespondentType ────────────────────────────────────────────

    public function test_respondent_type_values(): void
    {
        $this->assertSame('anonymous', RespondentType::ANONYMOUS->value);
        $this->assertSame('authenticated', RespondentType::AUTHENTICATED->value);
        $this->assertSame('email', RespondentType::EMAIL->value);
    }

    public function test_respondent_type_requires_auth(): void
    {
        $this->assertTrue(RespondentType::AUTHENTICATED->requiresAuth());
        $this->assertFalse(RespondentType::ANONYMOUS->requiresAuth());
    }

    public function test_respondent_type_collects_email(): void
    {
        $this->assertTrue(RespondentType::EMAIL->collectsEmail());
        $this->assertTrue(RespondentType::AUTHENTICATED->collectsEmail());
        $this->assertFalse(RespondentType::ANONYMOUS->collectsEmail());
    }

    // ── SurveyCategory ────────────────────────────────────────────

    public function test_survey_category_all_cases_covered(): void
    {
        $this->assertCount(13, SurveyCategory::cases());
    }

    public function test_survey_category_from_string(): void
    {
        $this->assertSame(SurveyCategory::NPS, SurveyCategory::fromString('nps'));
        $this->assertSame(SurveyCategory::GENERAL, SurveyCategory::fromString('unknown'));
    }

    public function test_survey_category_suggested_question_types(): void
    {
        $this->assertContains('nps', SurveyCategory::NPS->suggestedQuestionTypes());
        $this->assertContains('text', SurveyCategory::GENERAL->suggestedQuestionTypes());
    }

    // ── ShareChannel ──────────────────────────────────────────────

    public function test_share_channel_values(): void
    {
        $this->assertSame('email', ShareChannel::EMAIL->value);
        $this->assertSame('link', ShareChannel::LINK->value);
        $this->assertSame('embed', ShareChannel::EMBED->value);
        $this->assertSame('sms', ShareChannel::SMS->value);
        $this->assertSame('qr_code', ShareChannel::QR_CODE->value);
        $this->assertSame('social', ShareChannel::SOCIAL->value);
    }

    public function test_share_channel_requires_distribution_strategy(): void
    {
        $this->assertTrue(ShareChannel::EMAIL->requiresDistributionStrategy());
        $this->assertFalse(ShareChannel::LINK->requiresDistributionStrategy());
        $this->assertFalse(ShareChannel::QR_CODE->requiresDistributionStrategy());
    }

    public function test_share_channel_generates_public_url(): void
    {
        $this->assertTrue(ShareChannel::LINK->generatesPublicUrl());
        $this->assertTrue(ShareChannel::EMBED->generatesPublicUrl());
        $this->assertFalse(ShareChannel::EMAIL->generatesPublicUrl());
    }

    // ── AutomationTrigger ─────────────────────────────────────────

    public function test_automation_trigger_values(): void
    {
        $this->assertSame('response_created', AutomationTrigger::RESPONSE_CREATED->value);
        $this->assertSame('response_completed', AutomationTrigger::RESPONSE_COMPLETED->value);
        $this->assertSame('question_answered', AutomationTrigger::QUESTION_ANSWERED->value);
        $this->assertSame('survey_closed', AutomationTrigger::SURVEY_CLOSED->value);
        $this->assertSame('score_reached', AutomationTrigger::SCORE_REACHED->value);
    }

    public function test_automation_trigger_requires_conditions(): void
    {
        $this->assertTrue(AutomationTrigger::QUESTION_ANSWERED->requiresConditions());
        $this->assertTrue(AutomationTrigger::SCORE_REACHED->requiresConditions());
        $this->assertFalse(AutomationTrigger::RESPONSE_CREATED->requiresConditions());
    }

    public function test_automation_trigger_available_for_quiz_mode(): void
    {
        $this->assertTrue(AutomationTrigger::RESPONSE_CREATED->availableForQuizMode());
        $this->assertFalse(AutomationTrigger::SCORE_REACHED->availableForQuizMode());
    }

    // ── AutomationAction ──────────────────────────────────────────

    public function test_automation_action_values(): void
    {
        $this->assertSame('send_email', AutomationAction::SEND_EMAIL->value);
        $this->assertSame('update_field', AutomationAction::UPDATE_FIELD->value);
        $this->assertSame('create_activity', AutomationAction::CREATE_ACTIVITY->value);
        $this->assertSame('send_notification', AutomationAction::SEND_NOTIFICATION->value);
        $this->assertSame('trigger_webhook', AutomationAction::TRIGGER_WEBHOOK->value);
        $this->assertSame('create_crm_activity', AutomationAction::CREATE_CRM_ACTIVITY->value);
    }

    public function test_automation_action_requires_cross_module(): void
    {
        $this->assertTrue(AutomationAction::SEND_EMAIL->requiresCrossModuleIntegration());
        $this->assertTrue(AutomationAction::CREATE_CRM_ACTIVITY->requiresCrossModuleIntegration());
        $this->assertFalse(AutomationAction::UPDATE_FIELD->requiresCrossModuleIntegration());
    }

    public function test_automation_action_default_config(): void
    {
        $config = AutomationAction::SEND_EMAIL->defaultConfig();
        $this->assertArrayHasKey('to', $config);
        $this->assertArrayHasKey('template', $config);
    }

    // ── BranchingOperator ─────────────────────────────────────────

    public function test_branching_operator_all_cases_covered(): void
    {
        $this->assertCount(12, BranchingOperator::cases());
    }

    public function test_branching_operator_evaluate_equals(): void
    {
        $this->assertTrue(BranchingOperator::EQUALS->evaluate(5, 5));
        $this->assertFalse(BranchingOperator::EQUALS->evaluate(5, 10));
    }

    public function test_branching_operator_evaluate_contains(): void
    {
        $this->assertTrue(BranchingOperator::CONTAINS->evaluate('hello world', 'world'));
        $this->assertFalse(BranchingOperator::CONTAINS->evaluate('hello', 'world'));
    }

    public function test_branching_operator_evaluate_greater_than(): void
    {
        $this->assertTrue(BranchingOperator::GREATER_THAN->evaluate(10, 5));
        $this->assertFalse(BranchingOperator::GREATER_THAN->evaluate(5, 10));
    }

    public function test_branching_operator_evaluate_in(): void
    {
        $this->assertTrue(BranchingOperator::IN->evaluate('a', ['a', 'b', 'c']));
        $this->assertFalse(BranchingOperator::IN->evaluate('d', ['a', 'b', 'c']));
    }

    public function test_branching_operator_evaluate_is_empty(): void
    {
        $this->assertTrue(BranchingOperator::IS_EMPTY->evaluate('', null));
        $this->assertTrue(BranchingOperator::IS_EMPTY->evaluate(null, null));
        $this->assertFalse(BranchingOperator::IS_EMPTY->evaluate('hello', null));
    }

    public function test_branching_operator_suitable_for_type(): void
    {
        $this->assertTrue(BranchingOperator::EQUALS->suitableForType(QuestionType::TEXT));
        $this->assertTrue(BranchingOperator::CONTAINS->suitableForType(QuestionType::TEXT));
        $this->assertTrue(BranchingOperator::GREATER_THAN->suitableForType(QuestionType::NUMBER));
        $this->assertFalse(BranchingOperator::CONTAINS->suitableForType(QuestionType::NUMBER));
    }
}
