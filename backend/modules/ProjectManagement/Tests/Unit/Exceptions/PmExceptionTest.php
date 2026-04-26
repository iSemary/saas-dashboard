<?php

namespace Modules\ProjectManagement\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Modules\ProjectManagement\Domain\Exceptions\InvalidProjectStatusTransition;
use Modules\ProjectManagement\Domain\Exceptions\InvalidTaskStatusTransition;
use Modules\ProjectManagement\Domain\Exceptions\WipLimitExceeded;
use Modules\ProjectManagement\Domain\Exceptions\CircularDependencyDetected;

class PmExceptionTest extends TestCase
{
    // ── InvalidProjectStatusTransition ───────────────────────────

    public function test_invalid_project_status_transition_message(): void
    {
        $exception = new InvalidProjectStatusTransition('archived', 'active');
        $this->assertSame('Cannot transition project status from [archived] to [active].', $exception->getMessage());
    }

    public function test_invalid_project_status_transition_is_exception(): void
    {
        $exception = new InvalidProjectStatusTransition('completed', 'planning');
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    // ── InvalidTaskStatusTransition ──────────────────────────────

    public function test_invalid_task_status_transition_message(): void
    {
        $exception = new InvalidTaskStatusTransition('done', 'in_progress');
        $this->assertSame('Cannot transition task status from [done] to [in_progress].', $exception->getMessage());
    }

    // ── WipLimitExceeded ────────────────────────────────────────

    public function test_wip_limit_exceeded_message(): void
    {
        $exception = new WipLimitExceeded('In Progress', 5);
        $this->assertSame('WIP limit of 5 exceeded in column [In Progress].', $exception->getMessage());
    }

    // ── CircularDependencyDetected ──────────────────────────────

    public function test_circular_dependency_detected_message(): void
    {
        $exception = new CircularDependencyDetected('task-1', 'task-3');
        $this->assertSame('Adding dependency [task-3] to task [task-1] would create a circular dependency.', $exception->getMessage());
    }
}
