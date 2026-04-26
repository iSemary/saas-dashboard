<?php

namespace Modules\Expenses\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Modules\Expenses\Domain\Events\ExpenseCreated;
use Modules\Expenses\Domain\Events\ExpenseSubmitted;
use Modules\Expenses\Domain\Events\ExpenseApproved;
use Modules\Expenses\Domain\Events\ExpenseRejected;
use Modules\Expenses\Domain\Events\ExpenseReimbursed;
use Modules\Expenses\Domain\Events\ReportSubmitted;
use Modules\Expenses\Domain\Events\ReportApproved;
use Modules\Expenses\Domain\Events\ReportRejected;

class ExpensesEventTest extends TestCase
{
    private function makeEntity(): object
    {
        return new class {
            public int $id = 1;
        };
    }

    // ── ExpenseCreated ────────────────────────────────────────────

    public function test_expense_created_stores_entity(): void
    {
        $entity = $this->makeEntity();
        $event = new ExpenseCreated($entity);

        $this->assertSame($entity, $event->entity);
    }

    // ── ExpenseSubmitted ──────────────────────────────────────────

    public function test_expense_submitted_stores_entity(): void
    {
        $entity = $this->makeEntity();
        $event = new ExpenseSubmitted($entity);

        $this->assertSame($entity, $event->entity);
    }

    // ── ExpenseApproved ────────────────────────────────────────────

    public function test_expense_approved_stores_entity(): void
    {
        $entity = $this->makeEntity();
        $event = new ExpenseApproved($entity);

        $this->assertSame($entity, $event->entity);
    }

    // ── ExpenseRejected ───────────────────────────────────────────

    public function test_expense_rejected_stores_entity(): void
    {
        $entity = $this->makeEntity();
        $event = new ExpenseRejected($entity);

        $this->assertSame($entity, $event->entity);
    }

    // ── ExpenseReimbursed ──────────────────────────────────────────

    public function test_expense_reimbursed_stores_entity(): void
    {
        $entity = $this->makeEntity();
        $event = new ExpenseReimbursed($entity);

        $this->assertSame($entity, $event->entity);
    }

    // ── ReportSubmitted ────────────────────────────────────────────

    public function test_report_submitted_stores_entity(): void
    {
        $entity = $this->makeEntity();
        $event = new ReportSubmitted($entity);

        $this->assertSame($entity, $event->entity);
    }

    // ── ReportApproved ─────────────────────────────────────────────

    public function test_report_approved_stores_entity(): void
    {
        $entity = $this->makeEntity();
        $event = new ReportApproved($entity);

        $this->assertSame($entity, $event->entity);
    }

    // ── ReportRejected ─────────────────────────────────────────────

    public function test_report_rejected_stores_entity(): void
    {
        $entity = $this->makeEntity();
        $event = new ReportRejected($entity);

        $this->assertSame($entity, $event->entity);
    }
}
