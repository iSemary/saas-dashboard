<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\UseCases\AbTest;

use Modules\SmsMarketing\Infrastructure\Persistence\SmAbTestRepositoryInterface;
use Modules\SmsMarketing\Application\DTOs\AbTest\CreateSmAbTestDTO;
use Modules\SmsMarketing\Application\DTOs\AbTest\UpdateSmAbTestDTO;

class SmAbTestUseCase
{
    public function __construct(
        private readonly SmAbTestRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateSmAbTestDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateSmAbTestDTO $dto)
    {
        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function selectWinner(int $id, string $variant): void
    {
        $abTest = $this->repository->findOrFail($id);
        $abTest->update(['winner' => $variant]);
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
