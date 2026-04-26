<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Application\UseCases\TimeEntry;

use Modules\TimeManagement\Domain\Entities\TimeEntry;
use Modules\TimeManagement\Domain\ValueObjects\TimeEntryStatus;
use Modules\TimeManagement\Infrastructure\Persistence\TimeEntryRepositoryInterface;

class ChangeTimeEntryStatus
{
    public function __construct(
        private TimeEntryRepositoryInterface $repository
    ) {}

    public function execute(string $entryId, TimeEntryStatus $newStatus): TimeEntry
    {
        $entry = $this->repository->findOrFail($entryId);
        $entry->transitionStatus($newStatus);
        return $entry->fresh();
    }
}
