<?php

namespace Modules\HR\Application\DTOs;

use Modules\HR\Presentation\Http\Requests\UpdatePositionRequest;

readonly class UpdatePositionData
{
    public function __construct(
        public ?string $title,
        public ?string $code,
        public ?int $department_id,
        public ?string $level,
        public ?float $min_salary,
        public ?float $max_salary,
        public ?string $description,
        public ?string $requirements,
        public ?bool $is_active,
        public ?array $custom_fields,
    ) {}

    public static function fromRequest(UpdatePositionRequest $request): self
    {
        return new self(
            title: $request->validated('title'),
            code: $request->validated('code'),
            department_id: $request->validated('department_id'),
            level: $request->validated('level'),
            min_salary: $request->validated('min_salary'),
            max_salary: $request->validated('max_salary'),
            description: $request->validated('description'),
            requirements: $request->validated('requirements'),
            is_active: $request->validated('is_active'),
            custom_fields: $request->validated('custom_fields'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'code' => $this->code,
            'department_id' => $this->department_id,
            'level' => $this->level,
            'min_salary' => $this->min_salary,
            'max_salary' => $this->max_salary,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'is_active' => $this->is_active,
            'custom_fields' => $this->custom_fields,
        ], fn ($value) => $value !== null);
    }
}
