<?php

namespace Modules\HR\Application\DTOs;

readonly class ApproveLeaveData
{
    public function __construct(
        public int $leaveRequestId,
        public ?string $notes,
    ) {}
}
