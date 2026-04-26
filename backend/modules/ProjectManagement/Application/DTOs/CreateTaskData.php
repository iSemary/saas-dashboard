<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Application\DTOs;

class CreateTaskData
{
    public function __construct(
        public string $tenantId,
        public string $projectId,
        public string $title,
        public ?string $description = null,
        public ?string $milestoneId = null,
        public ?string $boardColumnId = null,
        public ?string $swimlaneId = null,
        public ?string $parentTaskId = null,
        public ?string $priority = null,
        public ?string $type = null,
        public ?string $startDate = null,
        public ?string $dueDate = null,
        public ?float $estimatedHours = null,
        public ?string $assigneeId = null,
        public ?string $createdBy = null,
    ) {}

    public static function fromRequest($request, string $projectId = null): self
    {
        return new self(
            tenantId: $request->input('tenant_id', $request->user()->tenant_id ?? ''),
            projectId: $projectId ?? $request->input('project_id'),
            title: $request->input('title'),
            description: $request->input('description'),
            milestoneId: $request->input('milestone_id'),
            boardColumnId: $request->input('board_column_id'),
            swimlaneId: $request->input('swimlane_id'),
            parentTaskId: $request->input('parent_task_id'),
            priority: $request->input('priority'),
            type: $request->input('type'),
            startDate: $request->input('start_date'),
            dueDate: $request->input('due_date'),
            estimatedHours: $request->input('estimated_hours'),
            assigneeId: $request->input('assignee_id'),
            createdBy: $request->user()->id,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'tenant_id' => $this->tenantId,
            'project_id' => $this->projectId,
            'title' => $this->title,
            'description' => $this->description,
            'milestone_id' => $this->milestoneId,
            'board_column_id' => $this->boardColumnId,
            'swimlane_id' => $this->swimlaneId,
            'parent_task_id' => $this->parentTaskId,
            'priority' => $this->priority,
            'type' => $this->type,
            'start_date' => $this->startDate,
            'due_date' => $this->dueDate,
            'estimated_hours' => $this->estimatedHours,
            'assignee_id' => $this->assigneeId,
            'created_by' => $this->createdBy,
        ], fn($value) => !is_null($value));
    }
}
