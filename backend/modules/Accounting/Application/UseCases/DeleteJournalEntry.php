<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\JournalEntryRepositoryInterface;

class DeleteJournalEntry
{
    public function __construct(
        private readonly JournalEntryRepositoryInterface $repository,
    ) {}

    public function execute(int \$id): bool
    {
        return \$this->repository->delete(\$id);
    }

}
