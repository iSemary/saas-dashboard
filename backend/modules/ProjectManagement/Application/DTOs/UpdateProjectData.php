<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Application\DTOs;

class UpdateProjectData
{
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
        public ?string $workspaceId = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?float $budget = null,
        public ?array $settings = null,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            name: $request->input('name'),
            description: $request->input('description'),
            workspaceId: $request->input('workspace_id'),
            startDate: $request->input('start_date'),
            endDate: $request->input('end_date'),
            budget: $request->input('budget'),
            settings: $request->input('settings'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'workspace_id' => $this->workspaceId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'budget' => $this->budget,
            'settings' => $this->settings,
        ], fn($value) => !is_null($value));
    }
}
