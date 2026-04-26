<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Strategies\AutomationAction;

class DefaultAutomationActionStrategy implements AutomationActionStrategyInterface
{
    public function execute(string $actionType, array $params): void
    {
        // Default: no-op. Real implementation dispatches jobs.
    }
}
