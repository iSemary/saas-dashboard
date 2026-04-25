<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Survey\Domain\Entities\Survey;

class SurveyClosed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Survey $survey
    ) {}
}
