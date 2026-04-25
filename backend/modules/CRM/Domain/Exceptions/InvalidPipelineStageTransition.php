<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Exceptions;

use RuntimeException;

class InvalidPipelineStageTransition extends RuntimeException
{
    public function __construct(string $fromStage, string $toStage, ?string $reason = null)
    {
        $message = "Cannot transition opportunity stage from '{$fromStage}' to '{$toStage}'";
        if ($reason) {
            $message .= ": {$reason}";
        }

        parent::__construct($message);
    }
}
