<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases\JournalEntry;

use Modules\Accounting\Infrastructure\Persistence\JournalEntryRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\JournalItemRepositoryInterface;
use Modules\Accounting\Application\DTOs\JournalEntry\CreateJournalEntryDTO;
use Modules\Accounting\Application\DTOs\JournalEntry\UpdateJournalEntryDTO;
use Modules\Accounting\Domain\ValueObjects\JournalEntryState;
use Modules\Accounting\Domain\Strategies\JournalValidation\JournalValidationStrategyInterface;

class JournalEntryUseCase
{
    public function __construct(
        private readonly JournalEntryRepositoryInterface $repository,
        private readonly JournalItemRepositoryInterface $itemRepository,
        private readonly JournalValidationStrategyInterface $validationStrategy,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateJournalEntryDTO $dto)
    {
        $entry = $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));

        foreach ($dto->items as $itemData) {
            $this->itemRepository->create(array_merge($itemData, ['journal_entry_id' => $entry->id]));
        }

        $entry->recalculateTotals();
        return $entry->fresh();
    }

    public function update(int $id, UpdateJournalEntryDTO $dto)
    {
        $entry = $this->repository->findOrFail($id);

        if (! $entry->isEditable()) {
            throw new \RuntimeException('Cannot update a posted or cancelled journal entry');
        }

        $this->repository->update($id, $dto->toArray());

        if ($dto->items !== null) {
            $entry->journalItems()->delete();
            foreach ($dto->items as $itemData) {
                $this->itemRepository->create(array_merge($itemData, ['journal_entry_id' => $id]));
            }
        }

        $entry->recalculateTotals();
        return $entry->fresh();
    }

    public function delete(int $id): bool
    {
        $entry = $this->repository->findOrFail($id);

        if (! $entry->isEditable()) {
            throw new \RuntimeException('Cannot delete a posted or cancelled journal entry');
        }

        return $this->repository->delete($id);
    }

    public function post(int $id): void
    {
        $entry = $this->repository->findOrFail($id);
        $this->validationStrategy->validate($entry);
        $entry->transitionState(JournalEntryState::POSTED);
    }

    public function cancel(int $id): void
    {
        $entry = $this->repository->findOrFail($id);
        $entry->transitionState(JournalEntryState::CANCELLED);
    }

    public function bulkDelete(array $ids): int
    {
        return $this->repository->bulkDelete($ids);
    }

    public function getTableList(array $params)
    {
        return $this->repository->getTableList($params);
    }
}
