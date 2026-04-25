<?php

namespace Modules\Sales\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Sales\Domain\Contracts\SalesClientRepositoryInterface;
use Modules\Sales\Domain\Entities\SalesClient;

class SalesClientService
{
    public function __construct(
        private readonly SalesClientRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): SalesClient
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, int $userId): SalesClient
    {
        $data['created_by'] = $userId;
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): SalesClient
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
