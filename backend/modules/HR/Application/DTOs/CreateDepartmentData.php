<?php

namespace Modules\HR\Application\DTOs;

use Modules\HR\Presentation\Http\Requests\StoreDepartmentRequest;

readonly class CreateDepartmentData
{
    public function __construct(
        public string $name,
        public ?string $code,
        public ?int $parent_id,
        public ?int $manager_id,
        public ?string $description,
        public string $status,
        public ?array $custom_fields,
    ) {}

    public static function fromRequest(StoreDepartmentRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            code: $request->validated('code'),
            parent_id: $request->validated('parent_id'),
            manager_id: $request->validated('manager_id'),
            description: $request->validated('description'),
            status: $request->validated('status', 'active'),
            custom_fields: $request->validated('custom_fields'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'parent_id' => $this->parent_id,
            'manager_id' => $this->manager_id,
            'description' => $this->description,
            'status' => $this->status,
            'custom_fields' => $this->custom_fields,
        ];
    }
}
