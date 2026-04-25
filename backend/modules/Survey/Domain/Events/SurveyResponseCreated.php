<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Survey\Domain\Entities\SurveyResponse;

class SurveyResponseCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public SurveyResponse $response
    ) {}
}
