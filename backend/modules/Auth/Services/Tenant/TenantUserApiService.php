<?php

namespace Modules\Auth\Services\Tenant;

use App\Models\User;
use Modules\Auth\Repositories\Tenant\TenantUserApiRepositoryInterface;

class TenantUserApiService
{
    public function __construct(protected TenantUserApiRepositoryInterface $repository) {}

    public function list(array $filters = [], int $perPage = 50)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function create(array $data): User
    {
        return $this->repository->create($data);
    }

    public function findOrFail(int $id): User
    {
        return $this->repository->findOrFail($id);
    }

    public function update(int $id, array $data): User
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function assignRoles(int $userId, array $roleIds): User
    {
        return $this->repository->assignRoles($userId, $roleIds);
    }
}
