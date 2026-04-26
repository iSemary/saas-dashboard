<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\UseCases\AbTest;

use Modules\EmailMarketing\Infrastructure\Persistence\EmAbTestRepositoryInterface;
use Modules\EmailMarketing\Application\DTOs\AbTest\CreateEmAbTestDTO;
use Modules\EmailMarketing\Application\DTOs\AbTest\UpdateEmAbTestDTO;

class EmAbTestUseCase
{
    public function __construct(
        private readonly EmAbTestRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateEmAbTestDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateEmAbTestDTO $dto)
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
