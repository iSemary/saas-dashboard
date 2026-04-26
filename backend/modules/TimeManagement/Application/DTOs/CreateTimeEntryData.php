<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Application\DTOs;

class CreateTimeEntryData
{
    public function __construct(
        public string $tenantId,
        public string $userId,
        public string $date,
        public int $durationMinutes,
        public ?string $projectId = null,
        public ?string $taskId = null,
        public ?string $startTime = null,
        public ?string $endTime = null,
        public string $source = 'manual',
        public bool $isBillable = true,
        public ?string $description = null,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            tenantId: $request->input('tenant_id', $request->user()->tenant_id ?? ''),
            userId: $request->input('user_id', $request->user()->id),
            date: $request->input('date'),
            durationMinutes: $request->input('duration_minutes'),
            projectId: $request->input('project_id'),
            taskId: $request->input('task_id'),
            startTime: $request->input('start_time'),
            endTime: $request->input('end_time'),
            source: $request->input('source', 'manual'),
            isBillable: $request->input('is_billable', true),
            description: $request->input('description'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'date' => $this->date,
            'duration_minutes' => $this->durationMinutes,
            'project_id' => $this->projectId,
            'task_id' => $this->taskId,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'source' => $this->source,
            'is_billable' => $this->isBillable,
            'description' => $this->description,
        ], fn($v) => $v !== null);
    }
}
