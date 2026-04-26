<?php

namespace Modules\ProjectManagement\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\ProjectManagement\Application\DTOs\CreateProjectData;
use Modules\ProjectManagement\Application\DTOs\UpdateProjectData;
use Modules\ProjectManagement\Application\DTOs\CreateTaskData;
use Modules\ProjectManagement\Domain\ValueObjects\ProjectStatus;
use Modules\ProjectManagement\Domain\ValueObjects\TaskStatus;
use Modules\ProjectManagement\Domain\ValueObjects\TaskPriority;

class ProjectManagementUnitTest extends TestCase
{
    // --- CreateProjectData ---

    public function test_create_project_data_can_be_instantiated(): void
    {
        $dto = new CreateProjectData(
            tenantId: 'tenant-1',
            name: 'New Project',
            description: 'Test project',
            workspaceId: 'ws-1',
            startDate: '2025-01-01',
            endDate: '2025-12-31',
            budget: 50000.00,
            settings: ['visibility' => 'private'],
            createdBy: 'user-1',
        );

        $this->assertEquals('New Project', $dto->name);
        $this->assertEquals('tenant-1', $dto->tenantId);
        $this->assertEquals('ws-1', $dto->workspaceId);
    }

    public function test_create_project_data_to_array_filters_nulls(): void
    {
        $dto = new CreateProjectData(
            tenantId: 'tenant-1',
            name: 'New Project',
        );

        $array = $dto->toArray();
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('tenant_id', $array);
        $this->assertArrayNotHasKey('description', $array);
        $this->assertArrayNotHasKey('workspace_id', $array);
    }

    // --- UpdateProjectData ---

    public function test_update_project_data_allows_partial(): void
    {
        $dto = new UpdateProjectData(
            name: 'Updated Name',
        );

        $array = $dto->toArray();
        $this->assertEquals('Updated Name', $array['name']);
        $this->assertArrayNotHasKey('description', $array);
    }

    // --- CreateTaskData ---

    public function test_create_task_data_can_be_instantiated(): void
    {
        $dto = new CreateTaskData(
            tenantId: 'tenant-1',
            projectId: 'proj-1',
            title: 'New Task',
            description: 'Test task',
            priority: TaskPriority::HIGH->value,
            createdBy: 'user-1',
        );

        $this->assertEquals('New Task', $dto->title);
        $this->assertEquals('proj-1', $dto->projectId);
        $this->assertEquals('high', $dto->priority);
    }

    public function test_create_task_data_to_array_filters_nulls(): void
    {
        $dto = new CreateTaskData(
            tenantId: 'tenant-1',
            projectId: 'proj-1',
            title: 'New Task',
        );

        $array = $dto->toArray();
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('project_id', $array);
        $this->assertArrayNotHasKey('description', $array);
    }

    // --- ProjectStatus ---

    public function test_project_status_values(): void
    {
        $this->assertEquals('planning', ProjectStatus::PLANNING->value);
        $this->assertEquals('active', ProjectStatus::ACTIVE->value);
        $this->assertEquals('on_hold', ProjectStatus::ON_HOLD->value);
        $this->assertEquals('completed', ProjectStatus::COMPLETED->value);
        $this->assertEquals('archived', ProjectStatus::ARCHIVED->value);
    }

    public function test_project_status_valid_transitions(): void
    {
        $this->assertTrue(ProjectStatus::canTransitionFrom(ProjectStatus::PLANNING, ProjectStatus::ACTIVE));
        $this->assertTrue(ProjectStatus::canTransitionFrom(ProjectStatus::ACTIVE, ProjectStatus::ON_HOLD));
        $this->assertTrue(ProjectStatus::canTransitionFrom(ProjectStatus::ACTIVE, ProjectStatus::COMPLETED));
        $this->assertTrue(ProjectStatus::canTransitionFrom(ProjectStatus::ON_HOLD, ProjectStatus::ACTIVE));
    }

    public function test_project_status_invalid_transitions(): void
    {
        $this->assertFalse(ProjectStatus::canTransitionFrom(ProjectStatus::ARCHIVED, ProjectStatus::ACTIVE));
        $this->assertFalse(ProjectStatus::canTransitionFrom(ProjectStatus::COMPLETED, ProjectStatus::PLANNING));
    }

    // --- TaskStatus ---

    public function test_task_status_values(): void
    {
        $this->assertEquals('todo', TaskStatus::TODO->value);
        $this->assertEquals('in_progress', TaskStatus::IN_PROGRESS->value);
        $this->assertEquals('in_review', TaskStatus::IN_REVIEW->value);
        $this->assertEquals('done', TaskStatus::DONE->value);
        $this->assertEquals('cancelled', TaskStatus::CANCELLED->value);
    }

    public function test_task_status_valid_transitions(): void
    {
        $this->assertTrue(TaskStatus::canTransitionFrom(TaskStatus::TODO, TaskStatus::IN_PROGRESS));
        $this->assertTrue(TaskStatus::canTransitionFrom(TaskStatus::IN_PROGRESS, TaskStatus::IN_REVIEW));
        $this->assertTrue(TaskStatus::canTransitionFrom(TaskStatus::IN_REVIEW, TaskStatus::DONE));
    }

    public function test_task_status_invalid_transitions(): void
    {
        $this->assertFalse(TaskStatus::canTransitionFrom(TaskStatus::DONE, TaskStatus::IN_PROGRESS));
    }

    // --- TaskPriority ---

    public function test_task_priority_values(): void
    {
        $this->assertEquals('low', TaskPriority::LOW->value);
        $this->assertEquals('medium', TaskPriority::MEDIUM->value);
        $this->assertEquals('high', TaskPriority::HIGH->value);
        $this->assertEquals('critical', TaskPriority::CRITICAL->value);
    }

    public function test_task_priority_weights(): void
    {
        $this->assertEquals(1, TaskPriority::LOW->weight());
        $this->assertEquals(2, TaskPriority::MEDIUM->weight());
        $this->assertEquals(3, TaskPriority::HIGH->weight());
        $this->assertEquals(4, TaskPriority::CRITICAL->weight());
    }
}
