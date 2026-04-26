<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Application\UseCases\TimeEntry;

use Modules\TimeManagement\Application\DTOs\CreateTimeEntryData;
use Modules\TimeManagement\Domain\Entities\TimeEntry;
use Modules\TimeManagement\Infrastructure\Persistence\TimeEntryRepositoryInterface;

class CreateTimeEntry
{
    public function __construct(
        private TimeEntryRepositoryInterface $repository
    ) {}

    public function execute(CreateTimeEntryData $data): TimeEntry
    {
        return $this->repository->create($data->toArray());
    }
}
