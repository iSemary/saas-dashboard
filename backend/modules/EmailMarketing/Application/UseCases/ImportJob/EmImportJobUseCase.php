<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\UseCases\ImportJob;

use Modules\EmailMarketing\Infrastructure\Persistence\EmImportJobRepositoryInterface;
use Modules\EmailMarketing\Application\DTOs\ImportJob\CreateEmImportJobDTO;
use Modules\EmailMarketing\Application\DTOs\ImportJob\UpdateEmImportJobDTO;
use Modules\EmailMarketing\Domain\Strategies\Import\EmailImportStrategyInterface;

class EmImportJobUseCase
{
    public function __construct(
        private readonly EmImportJobRepositoryInterface $repository,
        private readonly EmailImportStrategyInterface $importStrategy,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateEmImportJobDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), [
            'created_by' => auth()->id(),
            'status' => 'pending',
        ]));
    }

    public function update(int $id, UpdateEmImportJobDTO $dto)
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
