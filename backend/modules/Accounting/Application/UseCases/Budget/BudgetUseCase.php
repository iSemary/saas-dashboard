<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases\Budget;

use Modules\Accounting\Infrastructure\Persistence\BudgetRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\BudgetItemRepositoryInterface;
use Modules\Accounting\Application\DTOs\Budget\CreateBudgetDTO;
use Modules\Accounting\Application\DTOs\Budget\UpdateBudgetDTO;
use Modules\Accounting\Domain\ValueObjects\BudgetStatus;

class BudgetUseCase
{
    public function __construct(
        private readonly BudgetRepositoryInterface $repository,
        private readonly BudgetItemRepositoryInterface $itemRepository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateBudgetDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateBudgetDTO $dto)
    {
        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function activate(int $id): void
    {
        $budget = $this->repository->findOrFail($id);
        $budget->transitionStatus(BudgetStatus::ACTIVE);
    }

    public function archive(int $id): void
    {
        $budget = $this->repository->findOrFail($id);
        $budget->transitionStatus(BudgetStatus::ARCHIVED);
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
