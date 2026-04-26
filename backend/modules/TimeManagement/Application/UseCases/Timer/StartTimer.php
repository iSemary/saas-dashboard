<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Application\UseCases\Timer;

use Modules\TimeManagement\Domain\Entities\TimeSession;
use Modules\TimeManagement\Domain\Exceptions\TimerAlreadyRunning;

class StartTimer
{
    public function execute(string $userId, string $tenantId, ?string $projectId = null, ?string $taskId = null, ?string $description = null): TimeSession
    {
        return TimeSession::startForUser($userId, $tenantId, $projectId, $taskId, $description);
    }
}
