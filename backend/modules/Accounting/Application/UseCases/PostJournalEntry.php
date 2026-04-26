<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\JournalEntryRepositoryInterface;

class PostJournalEntry
{
    public function __construct(
        private readonly JournalEntryRepositoryInterface $repository,
    ) {}

    use Modules\Accounting\Domain\ValueObjects\JournalEntryState;
    use Modules\Accounting\Domain\Strategies\JournalValidation\JournalValidationStrategyInterface;

    public function __construct(
        private readonly JournalEntryRepositoryInterface \$repository,
        private readonly JournalValidationStrategyInterface \$validationStrategy,
    ) {}

    public function execute(int \$id): JournalEntry
    {
        \$entry = \$this->repository->findOrFail(\$id);
        \$this->validationStrategy->validate(\$entry);
        \$entry->transitionState(JournalEntryState::POSTED);
        return \$entry->fresh();
    }

}
