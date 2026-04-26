<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\ApprovalRouting;

interface ApprovalRoutingStrategyInterface
{
    public function getApprovers(string $tenantId, string $userId): array;
}
