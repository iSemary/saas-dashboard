<?php

namespace Modules\ProjectManagement\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Modules\ProjectManagement\Domain\Events\ProjectCreated;
use Modules\ProjectManagement\Domain\Events\ProjectStatusChanged;
use Modules\ProjectManagement\Domain\Events\TaskCreated;
use Modules\ProjectManagement\Domain\Events\TaskAssigned;
use Modules\ProjectManagement\Domain\Events\TaskMovedToColumn;
use Modules\ProjectManagement\Domain\Events\TaskStatusChanged;
use Modules\ProjectManagement\Domain\Events\MilestoneCompleted;
use Modules\ProjectManagement\Domain\ValueObjects\ProjectStatus;
use Modules\ProjectManagement\Domain\ValueObjects\TaskStatus;

class PmEventTest extends TestCase
{
    // ── ProjectCreated ──────────────────────────────────────────

    public function test_project_created_stores_project(): void
    {
        $project = $this->createMock(\Modules\ProjectManagement\Domain\Entities\Project::class);
        $event = new ProjectCreated($project);

        $this->assertSame($project, $event->project);
    }

    // ── ProjectStatusChanged ─────────────────────────────────────

    public function test_project_status_changed_stores_transitions(): void
    {
        $event = new ProjectStatusChanged('proj-1', ProjectStatus::PLANNING, ProjectStatus::ACTIVE);

        $this->assertSame('proj-1', $event->projectId);
        $this->assertSame(ProjectStatus::PLANNING, $event->from);
        $this->assertSame(ProjectStatus::ACTIVE, $event->to);
    }

    // ── TaskCreated ─────────────────────────────────────────────

    public function test_task_created_stores_ids(): void
    {
        $event = new TaskCreated('task-1', 'proj-1');

        $this->assertSame('task-1', $event->taskId);
        $this->assertSame('proj-1', $event->projectId);
    }

    // ── TaskAssigned ─────────────────────────────────────────────

    public function test_task_assigned_stores_assignee(): void
    {
        $event = new TaskAssigned('task-1', 'proj-1', 'user-5');

        $this->assertSame('task-1', $event->taskId);
        $this->assertSame('proj-1', $event->projectId);
        $this->assertSame('user-5', $event->assigneeId);
    }

    public function test_task_assigned_assignee_can_be_null(): void
    {
        $event = new TaskAssigned('task-1', 'proj-1', null);

        $this->assertNull($event->assigneeId);
    }

    // ── TaskMovedToColumn ───────────────────────────────────────

    public function test_task_moved_to_column_stores_columns(): void
    {
        $event = new TaskMovedToColumn('task-1', 'proj-1', 'col-todo', 'col-done');

        $this->assertSame('task-1', $event->taskId);
        $this->assertSame('proj-1', $event->projectId);
        $this->assertSame('col-todo', $event->fromColumnId);
        $this->assertSame('col-done', $event->toColumnId);
    }

    public function test_task_moved_to_column_from_can_be_null(): void
    {
        $event = new TaskMovedToColumn('task-1', 'proj-1', null, 'col-todo');

        $this->assertNull($event->fromColumnId);
    }

    // ── TaskStatusChanged ───────────────────────────────────────

    public function test_task_status_changed_stores_transitions(): void
    {
        $event = new TaskStatusChanged('task-1', 'proj-1', TaskStatus::TODO, TaskStatus::IN_PROGRESS);

        $this->assertSame('task-1', $event->taskId);
        $this->assertSame('proj-1', $event->projectId);
        $this->assertSame(TaskStatus::TODO, $event->from);
        $this->assertSame(TaskStatus::IN_PROGRESS, $event->to);
    }

    // ── MilestoneCompleted ───────────────────────────────────────

    public function test_milestone_completed_stores_ids(): void
    {
        $event = new MilestoneCompleted('ms-1', 'proj-1');

        $this->assertSame('ms-1', $event->milestoneId);
        $this->assertSame('proj-1', $event->projectId);
    }
}
