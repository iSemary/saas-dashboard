<?php

namespace Modules\Expenses\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Modules\Expenses\Domain\Exceptions\InvalidExpenseTransition;
use Modules\Expenses\Domain\Exceptions\PolicyViolation;

class ExpensesExceptionTest extends TestCase
{
    // ── InvalidExpenseTransition ──────────────────────────────────

    public function test_invalid_expense_transition_message(): void
    {
        $exception = new InvalidExpenseTransition('reimbursed', 'draft');
        $this->assertSame("Cannot transition expense from 'reimbursed' to 'draft'", $exception->getMessage());
    }

    public function test_invalid_expense_transition_is_runtime_exception(): void
    {
        $exception = new InvalidExpenseTransition('cancelled', 'approved');
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    // ── PolicyViolation ──────────────────────────────────────────

    public function test_policy_violation_message(): void
    {
        $exception = new PolicyViolation('Expense amount exceeds maximum allowed');
        $this->assertSame('Expense amount exceeds maximum allowed', $exception->getMessage());
    }

    public function test_policy_violation_is_runtime_exception(): void
    {
        $exception = new PolicyViolation('Receipt required');
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }
}
