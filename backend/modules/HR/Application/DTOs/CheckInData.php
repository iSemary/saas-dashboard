<?php

namespace Modules\HR\Application\DTOs;

readonly class CheckInData
{
    public function __construct(
        public int $employeeId,
        public ?string $ipAddress,
        public ?float $latitude,
        public ?float $longitude,
        public ?string $notes,
    ) {}

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employeeId,
            'ip_address' => $this->ipAddress,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'notes' => $this->notes,
        ];
    }
}
