<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Strategies\AutomationAction;

interface AutomationActionStrategyInterface
{
    public function execute(string $actionType, array $params): void;
}
