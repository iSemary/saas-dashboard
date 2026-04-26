<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\JournalEntryRepositoryInterface;

class CancelJournalEntry
{
    public function __construct(
        private readonly JournalEntryRepositoryInterface $repository,
    ) {}

    use Modules\Accounting\Domain\ValueObjects\JournalEntryState;

    public function execute(int \$id): JournalEntry
    {
        \$entry = \$this->repository->findOrFail(\$id);
        \$entry->transitionState(JournalEntryState::CANCELLED);
        return \$entry->fresh();
    }

}
