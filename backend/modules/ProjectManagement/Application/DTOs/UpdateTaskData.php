<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Application\DTOs;

class UpdateTaskData
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $milestoneId = null,
        public ?string $boardColumnId = null,
        public ?string $swimlaneId = null,
        public ?string $priority = null,
        public ?string $type = null,
        public ?string $startDate = null,
        public ?string $dueDate = null,
        public ?float $estimatedHours = null,
        public ?float $actualHours = null,
        public ?string $assigneeId = null,
        public ?float $position = null,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            title: $request->input('title'),
            description: $request->input('description'),
            milestoneId: $request->input('milestone_id'),
            boardColumnId: $request->input('board_column_id'),
            swimlaneId: $request->input('swimlane_id'),
            priority: $request->input('priority'),
            type: $request->input('type'),
            startDate: $request->input('start_date'),
            dueDate: $request->input('due_date'),
            estimatedHours: $request->input('estimated_hours'),
            actualHours: $request->input('actual_hours'),
            assigneeId: $request->input('assignee_id'),
            position: $request->input('position'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'milestone_id' => $this->milestoneId,
            'board_column_id' => $this->boardColumnId,
            'swimlane_id' => $this->swimlaneId,
            'priority' => $this->priority,
            'type' => $this->type,
            'start_date' => $this->startDate,
            'due_date' => $this->dueDate,
            'estimated_hours' => $this->estimatedHours,
            'actual_hours' => $this->actualHours,
            'assignee_id' => $this->assigneeId,
            'position' => $this->position,
        ], fn($value) => !is_null($value));
    }
}
