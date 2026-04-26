<?php

namespace Modules\Expenses\Tests\Unit;

use Modules\Expenses\Domain\ValueObjects\ExpenseStatus;
use PHPUnit\Framework\TestCase;

class ExpenseStateMachineTest extends TestCase
{
    public function test_draft_can_transition_to_pending(): void
    {
        $this->assertTrue(ExpenseStatus::canTransitionFrom('draft', ExpenseStatus::PENDING));
    }

    public function test_draft_can_transition_to_cancelled(): void
    {
        $this->assertTrue(ExpenseStatus::canTransitionFrom('draft', ExpenseStatus::CANCELLED));
    }

    public function test_draft_cannot_transition_to_approved(): void
    {
        $this->assertFalse(ExpenseStatus::canTransitionFrom('draft', ExpenseStatus::APPROVED));
    }

    public function test_pending_can_transition_to_approved(): void
    {
        $this->assertTrue(ExpenseStatus::canTransitionFrom('pending', ExpenseStatus::APPROVED));
    }

    public function test_pending_can_transition_to_rejected(): void
    {
        $this->assertTrue(ExpenseStatus::canTransitionFrom('pending', ExpenseStatus::REJECTED));
    }

    public function test_approved_can_transition_to_reimbursed(): void
    {
        $this->assertTrue(ExpenseStatus::canTransitionFrom('approved', ExpenseStatus::REIMBURSED));
    }

    public function test_rejected_can_resubmit_to_pending(): void
    {
        $this->assertTrue(ExpenseStatus::canTransitionFrom('rejected', ExpenseStatus::PENDING));
    }

    public function test_reimbursed_is_terminal(): void
    {
        $this->assertFalse(ExpenseStatus::canTransitionFrom('reimbursed', ExpenseStatus::DRAFT));
        $this->assertFalse(ExpenseStatus::canTransitionFrom('reimbursed', ExpenseStatus::PENDING));
        $this->assertFalse(ExpenseStatus::canTransitionFrom('reimbursed', ExpenseStatus::APPROVED));
    }

    public function test_cancelled_is_terminal(): void
    {
        $this->assertFalse(ExpenseStatus::canTransitionFrom('cancelled', ExpenseStatus::DRAFT));
        $this->assertFalse(ExpenseStatus::canTransitionFrom('cancelled', ExpenseStatus::PENDING));
    }
}
