<?php

namespace Modules\Auth\Services\Tenant;

use Modules\Auth\Repositories\Tenant\TenantPermissionApiRepositoryInterface;
use Spatie\Permission\Models\Permission;

class TenantPermissionApiService
{
    public function __construct(protected TenantPermissionApiRepositoryInterface $repository) {}

    public function list(array $filters = [], int $perPage = 50)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function create(array $data): Permission
    {
        return $this->repository->create($data);
    }

    public function findOrFail(int $id): Permission
    {
        return $this->repository->findOrFail($id);
    }

    public function update(int $id, array $data): Permission
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
