<?php

namespace Modules\HR\Application\DTOs;

readonly class RejectLeaveData
{
    public function __construct(
        public int $leaveRequestId,
        public string $reason,
    ) {}
}
