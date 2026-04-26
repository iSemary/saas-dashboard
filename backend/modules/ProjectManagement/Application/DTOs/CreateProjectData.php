<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Application\DTOs;

class CreateProjectData
{
    public function __construct(
        public string $tenantId,
        public string $name,
        public ?string $description = null,
        public ?string $workspaceId = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?float $budget = null,
        public ?array $settings = null,
        public ?string $createdBy = null,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            tenantId: $request->input('tenant_id', $request->user()->tenant_id ?? ''),
            name: $request->input('name'),
            description: $request->input('description'),
            workspaceId: $request->input('workspace_id'),
            startDate: $request->input('start_date'),
            endDate: $request->input('end_date'),
            budget: $request->input('budget'),
            settings: $request->input('settings'),
            createdBy: $request->user()->id,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'tenant_id' => $this->tenantId,
            'name' => $this->name,
            'description' => $this->description,
            'workspace_id' => $this->workspaceId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'budget' => $this->budget,
            'settings' => $this->settings,
            'created_by' => $this->createdBy,
        ], fn($value) => !is_null($value));
    }
}
