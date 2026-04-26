<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases\Reconciliation;

use Modules\Accounting\Infrastructure\Persistence\ReconciliationRepositoryInterface;
use Modules\Accounting\Application\DTOs\Reconciliation\CreateReconciliationDTO;
use Modules\Accounting\Application\DTOs\Reconciliation\UpdateReconciliationDTO;

class ReconciliationUseCase
{
    public function __construct(
        private readonly ReconciliationRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateReconciliationDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateReconciliationDTO $dto)
    {
        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function start(int $id): void
    {
        $reconciliation = $this->repository->findOrFail($id);
        $reconciliation->start();
    }

    public function complete(int $id): void
    {
        $reconciliation = $this->repository->findOrFail($id);
        $reconciliation->complete();
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
