<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Exceptions;

use RuntimeException;

class AutomationExecutionException extends RuntimeException
{
    public function __construct(
        string $message,
        ?string $actionType = null,
        ?int $automationRuleId = null,
        ?\Throwable $previous = null
    ) {
        $fullMessage = $message;

        if ($actionType) {
            $fullMessage = "[{$actionType}] {$message}";
        }

        if ($automationRuleId) {
            $fullMessage .= " (Rule ID: {$automationRuleId})";
        }

        parent::__construct($fullMessage, 0, $previous);
    }
}
