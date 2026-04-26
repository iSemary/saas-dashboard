<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Application\UseCases\Timer;

use Modules\TimeManagement\Domain\Entities\TimeSession;
use Modules\TimeManagement\Infrastructure\Persistence\TimeEntryRepositoryInterface;
use Modules\TimeManagement\Application\DTOs\CreateTimeEntryData;

class StopTimer
{
    public function __construct(
        private TimeEntryRepositoryInterface $entryRepository
    ) {}

    public function execute(string $sessionId): TimeSession
    {
        $session = TimeSession::findOrFail($sessionId);
        $session->stop();

        if ($session->duration_seconds > 0) {
            $this->entryRepository->create([
                'tenant_id' => $session->tenant_id,
                'user_id' => $session->user_id,
                'project_id' => $session->project_id,
                'task_id' => $session->task_id,
                'date' => $session->started_at->toDateString(),
                'start_time' => $session->started_at->format('H:i:s'),
                'end_time' => $session->stopped_at->format('H:i:s'),
                'duration_minutes' => (int) ceil($session->duration_seconds / 60),
                'source' => 'timer',
                'time_session_id' => $session->id,
            ]);
        }

        return $session->fresh();
    }
}
