<?php

namespace Modules\CRM\tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Modules\CRM\Domain\Exceptions\InvalidLeadStatusTransition;
use Modules\CRM\Domain\Exceptions\LeadAlreadyConvertedException;
use Modules\CRM\Domain\Exceptions\InvalidPipelineStageTransition;
use Modules\CRM\Domain\Exceptions\DuplicateContactException;
use Modules\CRM\Domain\Exceptions\AutomationExecutionException;
use Modules\CRM\Domain\Exceptions\ImportValidationException;

class CrmExceptionTest extends TestCase
{
    // ── InvalidLeadStatusTransition ──────────────────────────────

    public function test_invalid_lead_status_transition_message(): void
    {
        $exception = new InvalidLeadStatusTransition('converted', 'new');
        $this->assertSame("Cannot transition lead status from 'converted' to 'new'", $exception->getMessage());
    }

    public function test_invalid_lead_status_transition_is_runtime_exception(): void
    {
        $exception = new InvalidLeadStatusTransition('new', 'approved');
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    // ── LeadAlreadyConvertedException ─────────────────────────────

    public function test_lead_already_converted_message_without_id(): void
    {
        $exception = new LeadAlreadyConvertedException();
        $this->assertSame('Lead has already been converted to an opportunity', $exception->getMessage());
    }

    public function test_lead_already_converted_message_with_id(): void
    {
        $exception = new LeadAlreadyConvertedException(42);
        $this->assertSame('Lead has already been converted to an opportunity (Lead ID: 42)', $exception->getMessage());
    }

    // ── InvalidPipelineStageTransition ────────────────────────────

    public function test_invalid_pipeline_stage_transition_message_without_reason(): void
    {
        $exception = new InvalidPipelineStageTransition('closed_won', 'prospecting');
        $this->assertSame("Cannot transition opportunity stage from 'closed_won' to 'prospecting'", $exception->getMessage());
    }

    public function test_invalid_pipeline_stage_transition_message_with_reason(): void
    {
        $exception = new InvalidPipelineStageTransition('closed_won', 'prospecting', 'Cannot transition from a closed stage');
        $this->assertSame("Cannot transition opportunity stage from 'closed_won' to 'prospecting': Cannot transition from a closed stage", $exception->getMessage());
    }

    // ── DuplicateContactException ────────────────────────────────

    public function test_duplicate_contact_message_without_id(): void
    {
        $exception = new DuplicateContactException('email', 'john@example.com');
        $this->assertSame("A contact with email 'john@example.com' already exists", $exception->getMessage());
    }

    public function test_duplicate_contact_message_with_id(): void
    {
        $exception = new DuplicateContactException('phone', '+1234567890', 15);
        $this->assertSame("A contact with phone '+1234567890' already exists (Contact ID: 15)", $exception->getMessage());
    }

    // ── AutomationExecutionException ─────────────────────────────

    public function test_automation_execution_message_plain(): void
    {
        $exception = new AutomationExecutionException('Action failed');
        $this->assertSame('Action failed', $exception->getMessage());
    }

    public function test_automation_execution_message_with_action_type(): void
    {
        $exception = new AutomationExecutionException('Action failed', 'send_email');
        $this->assertSame('[send_email] Action failed', $exception->getMessage());
    }

    public function test_automation_execution_message_with_rule_id(): void
    {
        $exception = new AutomationExecutionException('Action failed', null, 7);
        $this->assertSame('Action failed (Rule ID: 7)', $exception->getMessage());
    }

    public function test_automation_execution_message_with_all_context(): void
    {
        $exception = new AutomationExecutionException('Timeout', 'send_sms', 3);
        $this->assertSame('[send_sms] Timeout (Rule ID: 3)', $exception->getMessage());
    }

    // ── ImportValidationException ────────────────────────────────

    public function test_import_validation_default_message(): void
    {
        $errors = ['Row 1: Invalid email', 'Row 3: Missing name'];
        $exception = new ImportValidationException($errors);
        $this->assertSame('Import validation failed with 2 error(s)', $exception->getMessage());
    }

    public function test_import_validation_custom_message(): void
    {
        $exception = new ImportValidationException(['error1'], 'Custom error message');
        $this->assertSame('Custom error message', $exception->getMessage());
    }

    public function test_import_validation_get_errors(): void
    {
        $errors = ['Row 1: Invalid email', 'Row 3: Missing name'];
        $exception = new ImportValidationException($errors);
        $this->assertSame($errors, $exception->getErrors());
    }

    public function test_import_validation_get_error_count(): void
    {
        $errors = ['error1', 'error2', 'error3'];
        $exception = new ImportValidationException($errors);
        $this->assertSame(3, $exception->getErrorCount());
    }
}
