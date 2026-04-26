<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnomalyDetected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $userId,
        public string $anomalyType,
        public array $details = [],
    ) {}
}
