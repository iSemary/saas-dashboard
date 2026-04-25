<?php

namespace Modules\HR\Application\DTOs;

readonly class CheckOutData
{
    public function __construct(
        public int $attendanceId,
        public ?string $notes,
    ) {}
}
