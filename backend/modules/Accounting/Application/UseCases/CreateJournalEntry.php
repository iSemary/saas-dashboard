<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\JournalEntryRepositoryInterface;

class CreateJournalEntry
{
    public function __construct(
        private readonly JournalEntryRepositoryInterface $repository,
    ) {}

    public function execute(array \$data): JournalEntry
    {
        return \$this->repository->create(\$data);
    }

}
