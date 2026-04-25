<?php

namespace Modules\HR\Application\DTOs;

readonly class CreateGoalData
{
    public function __construct(
        public int $performanceCycleId,
        public int $employeeId,
        public ?int $managerId,
        public string $title,
        public ?string $description,
        public ?string $category,
        public float $weight,
        public string $startDate,
        public string $dueDate,
    ) {}

    public function toArray(): array
    {
        return [
            'performance_cycle_id' => $this->performanceCycleId,
            'employee_id' => $this->employeeId,
            'manager_id' => $this->managerId,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'weight' => $this->weight,
            'start_date' => $this->startDate,
            'due_date' => $this->dueDate,
        ];
    }
}
