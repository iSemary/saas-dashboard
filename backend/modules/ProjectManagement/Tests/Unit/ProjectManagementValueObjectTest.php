<?php

namespace Modules\ProjectManagement\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\ProjectManagement\Domain\ValueObjects\ProjectStatus;
use Modules\ProjectManagement\Domain\ValueObjects\TaskStatus;
use Modules\ProjectManagement\Domain\ValueObjects\TaskPriority;
use Modules\ProjectManagement\Domain\ValueObjects\ProjectHealth;
use Modules\ProjectManagement\Domain\ValueObjects\DependencyType;

class ProjectManagementValueObjectTest extends TestCase
{
    // ── ProjectStatus ──────────────────────────────────────────────

    public function test_project_status_values(): void
    {
        $this->assertSame('planning', ProjectStatus::PLANNING->value);
        $this->assertSame('active', ProjectStatus::ACTIVE->value);
        $this->assertSame('on_hold', ProjectStatus::ON_HOLD->value);
        $this->assertSame('completed', ProjectStatus::COMPLETED->value);
        $this->assertSame('archived', ProjectStatus::ARCHIVED->value);
    }

    public function test_project_status_all_cases_covered(): void
    {
        $this->assertCount(5, ProjectStatus::cases());
    }

    public function test_project_status_label(): void
    {
        $this->assertSame('Planning', ProjectStatus::PLANNING->label());
        $this->assertSame('Active', ProjectStatus::ACTIVE->label());
        $this->assertSame('On Hold', ProjectStatus::ON_HOLD->label());
        $this->assertSame('Completed', ProjectStatus::COMPLETED->label());
        $this->assertSame('Archived', ProjectStatus::ARCHIVED->label());
    }

    public function test_project_status_color(): void
    {
        $this->assertSame('blue', ProjectStatus::PLANNING->color());
        $this->assertSame('green', ProjectStatus::ACTIVE->color());
    }

    public function test_project_status_from_string_valid(): void
    {
        $this->assertSame(ProjectStatus::ACTIVE, ProjectStatus::fromString('active'));
    }

    public function test_project_status_from_string_invalid_defaults(): void
    {
        $this->assertSame(ProjectStatus::PLANNING, ProjectStatus::fromString('nonexistent'));
    }

    public function test_project_status_valid_transitions(): void
    {
        $this->assertTrue(ProjectStatus::canTransitionFrom(ProjectStatus::PLANNING, ProjectStatus::ACTIVE));
        $this->assertTrue(ProjectStatus::canTransitionFrom(ProjectStatus::PLANNING, ProjectStatus::ARCHIVED));
        $this->assertTrue(ProjectStatus::canTransitionFrom(ProjectStatus::ACTIVE, ProjectStatus::ON_HOLD));
        $this->assertTrue(ProjectStatus::canTransitionFrom(ProjectStatus::ACTIVE, ProjectStatus::COMPLETED));
        $this->assertTrue(ProjectStatus::canTransitionFrom(ProjectStatus::ACTIVE, ProjectStatus::ARCHIVED));
        $this->assertTrue(ProjectStatus::canTransitionFrom(ProjectStatus::ON_HOLD, ProjectStatus::ACTIVE));
        $this->assertTrue(ProjectStatus::canTransitionFrom(ProjectStatus::COMPLETED, ProjectStatus::ARCHIVED));
    }

    public function test_project_status_invalid_transitions(): void
    {
        $this->assertFalse(ProjectStatus::canTransitionFrom(ProjectStatus::ARCHIVED, ProjectStatus::ACTIVE));
        $this->assertFalse(ProjectStatus::canTransitionFrom(ProjectStatus::COMPLETED, ProjectStatus::PLANNING));
        $this->assertFalse(ProjectStatus::canTransitionFrom(ProjectStatus::PLANNING, ProjectStatus::COMPLETED));
        $this->assertFalse(ProjectStatus::canTransitionFrom(ProjectStatus::ARCHIVED, ProjectStatus::PLANNING));
    }

    // ── TaskStatus ────────────────────────────────────────────────

    public function test_task_status_values(): void
    {
        $this->assertSame('todo', TaskStatus::TODO->value);
        $this->assertSame('in_progress', TaskStatus::IN_PROGRESS->value);
        $this->assertSame('in_review', TaskStatus::IN_REVIEW->value);
        $this->assertSame('done', TaskStatus::DONE->value);
        $this->assertSame('cancelled', TaskStatus::CANCELLED->value);
    }

    public function test_task_status_all_cases_covered(): void
    {
        $this->assertCount(5, TaskStatus::cases());
    }

    public function test_task_status_label(): void
    {
        $this->assertSame('To Do', TaskStatus::TODO->label());
        $this->assertSame('In Progress', TaskStatus::IN_PROGRESS->label());
        $this->assertSame('In Review', TaskStatus::IN_REVIEW->label());
        $this->assertSame('Done', TaskStatus::DONE->label());
        $this->assertSame('Cancelled', TaskStatus::CANCELLED->label());
    }

    public function test_task_status_from_string_valid(): void
    {
        $this->assertSame(TaskStatus::IN_PROGRESS, TaskStatus::fromString('in_progress'));
    }

    public function test_task_status_from_string_invalid_defaults(): void
    {
        $this->assertSame(TaskStatus::TODO, TaskStatus::fromString('nonexistent'));
    }

    public function test_task_status_valid_transitions(): void
    {
        $this->assertTrue(TaskStatus::canTransitionFrom(TaskStatus::TODO, TaskStatus::IN_PROGRESS));
        $this->assertTrue(TaskStatus::canTransitionFrom(TaskStatus::TODO, TaskStatus::CANCELLED));
        $this->assertTrue(TaskStatus::canTransitionFrom(TaskStatus::IN_PROGRESS, TaskStatus::IN_REVIEW));
        $this->assertTrue(TaskStatus::canTransitionFrom(TaskStatus::IN_PROGRESS, TaskStatus::TODO));
        $this->assertTrue(TaskStatus::canTransitionFrom(TaskStatus::IN_REVIEW, TaskStatus::DONE));
        $this->assertTrue(TaskStatus::canTransitionFrom(TaskStatus::DONE, TaskStatus::TODO));
        $this->assertTrue(TaskStatus::canTransitionFrom(TaskStatus::CANCELLED, TaskStatus::TODO));
    }

    public function test_task_status_invalid_transitions(): void
    {
        $this->assertFalse(TaskStatus::canTransitionFrom(TaskStatus::TODO, TaskStatus::DONE));
        $this->assertFalse(TaskStatus::canTransitionFrom(TaskStatus::DONE, TaskStatus::IN_PROGRESS));
    }

    // ── TaskPriority ──────────────────────────────────────────────

    public function test_task_priority_values(): void
    {
        $this->assertSame('low', TaskPriority::LOW->value);
        $this->assertSame('medium', TaskPriority::MEDIUM->value);
        $this->assertSame('high', TaskPriority::HIGH->value);
        $this->assertSame('critical', TaskPriority::CRITICAL->value);
    }

    public function test_task_priority_all_cases_covered(): void
    {
        $this->assertCount(4, TaskPriority::cases());
    }

    public function test_task_priority_weights(): void
    {
        $this->assertSame(1, TaskPriority::LOW->weight());
        $this->assertSame(2, TaskPriority::MEDIUM->weight());
        $this->assertSame(3, TaskPriority::HIGH->weight());
        $this->assertSame(4, TaskPriority::CRITICAL->weight());
    }

    public function test_task_priority_from_string_invalid_defaults(): void
    {
        $this->assertSame(TaskPriority::MEDIUM, TaskPriority::fromString('nonexistent'));
    }

    // ── ProjectHealth ─────────────────────────────────────────────

    public function test_project_health_values(): void
    {
        $this->assertSame('on_track', ProjectHealth::ON_TRACK->value);
        $this->assertSame('at_risk', ProjectHealth::AT_RISK->value);
        $this->assertSame('off_track', ProjectHealth::OFF_TRACK->value);
    }

    public function test_project_health_all_cases_covered(): void
    {
        $this->assertCount(3, ProjectHealth::cases());
    }

    public function test_project_health_label(): void
    {
        $this->assertSame('On Track', ProjectHealth::ON_TRACK->label());
        $this->assertSame('At Risk', ProjectHealth::AT_RISK->label());
        $this->assertSame('Off Track', ProjectHealth::OFF_TRACK->label());
    }

    public function test_project_health_from_score(): void
    {
        $this->assertSame(ProjectHealth::ON_TRACK, ProjectHealth::fromScore(85.0));
        $this->assertSame(ProjectHealth::ON_TRACK, ProjectHealth::fromScore(70.0));
        $this->assertSame(ProjectHealth::AT_RISK, ProjectHealth::fromScore(55.0));
        $this->assertSame(ProjectHealth::AT_RISK, ProjectHealth::fromScore(40.0));
        $this->assertSame(ProjectHealth::OFF_TRACK, ProjectHealth::fromScore(25.0));
        $this->assertSame(ProjectHealth::OFF_TRACK, ProjectHealth::fromScore(0.0));
    }

    public function test_project_health_from_string_invalid_defaults(): void
    {
        $this->assertSame(ProjectHealth::ON_TRACK, ProjectHealth::fromString('nonexistent'));
    }

    // ── DependencyType ────────────────────────────────────────────

    public function test_dependency_type_values(): void
    {
        $this->assertSame('finish_to_start', DependencyType::FINISH_TO_START->value);
        $this->assertSame('start_to_start', DependencyType::START_TO_START->value);
        $this->assertSame('finish_to_finish', DependencyType::FINISH_TO_FINISH->value);
        $this->assertSame('start_to_finish', DependencyType::START_TO_FINISH->value);
    }

    public function test_dependency_type_all_cases_covered(): void
    {
        $this->assertCount(4, DependencyType::cases());
    }

    public function test_dependency_type_label(): void
    {
        $this->assertSame('Finish to Start', DependencyType::FINISH_TO_START->label());
        $this->assertSame('Start to Start', DependencyType::START_TO_START->label());
        $this->assertSame('Finish to Finish', DependencyType::FINISH_TO_FINISH->label());
        $this->assertSame('Start to Finish', DependencyType::START_TO_FINISH->label());
    }

    public function test_dependency_type_from_string_invalid_defaults(): void
    {
        $this->assertSame(DependencyType::FINISH_TO_START, DependencyType::fromString('nonexistent'));
    }
}
