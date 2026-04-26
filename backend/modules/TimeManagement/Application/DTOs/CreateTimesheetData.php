<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Application\DTOs;

class CreateTimesheetData
{
    public function __construct(
        public string $tenantId,
        public string $userId,
        public string $periodStart,
        public string $periodEnd,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            tenantId: $request->input('tenant_id', $request->user()->tenant_id ?? ''),
            userId: $request->input('user_id', $request->user()->id),
            periodStart: $request->input('period_start'),
            periodEnd: $request->input('period_end'),
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'period_start' => $this->periodStart,
            'period_end' => $this->periodEnd,
        ];
    }
}
