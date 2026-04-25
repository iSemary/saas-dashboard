<?php

namespace Modules\HR\Application\DTOs;

readonly class GeneratePayrollData
{
    public function __construct(
        public int $employeeId,
        public string $payPeriodStart,
        public string $payPeriodEnd,
        public string $payDate,
        public ?string $notes,
    ) {}

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employeeId,
            'pay_period_start' => $this->payPeriodStart,
            'pay_period_end' => $this->payPeriodEnd,
            'pay_date' => $this->payDate,
            'notes' => $this->notes,
        ];
    }
}
