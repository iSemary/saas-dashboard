<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\UseCases\ImportJob;

use Modules\SmsMarketing\Infrastructure\Persistence\SmImportJobRepositoryInterface;
use Modules\SmsMarketing\Application\DTOs\ImportJob\CreateSmImportJobDTO;
use Modules\SmsMarketing\Application\DTOs\ImportJob\UpdateSmImportJobDTO;
use Modules\SmsMarketing\Domain\Strategies\Import\SmsImportStrategyInterface;

class SmImportJobUseCase
{
    public function __construct(
        private readonly SmImportJobRepositoryInterface $repository,
        private readonly SmsImportStrategyInterface $importStrategy,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateSmImportJobDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), [
            'created_by' => auth()->id(),
            'status' => 'pending',
        ]));
    }

    public function update(int $id, UpdateSmImportJobDTO $dto)
    {
        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function process(int $id): void
    {
        $job = $this->repository->findOrFail($id);
        $this->importStrategy->import($job);
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
