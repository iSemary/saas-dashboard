<?php

namespace Modules\Auth\Services\Tenant;

use Modules\Auth\Repositories\Tenant\TenantRoleApiRepositoryInterface;
use Spatie\Permission\Models\Role;

class TenantRoleApiService
{
    public function __construct(protected TenantRoleApiRepositoryInterface $repository) {}

    public function list(array $params = [])
    {
        return $this->repository->list($params);
    }

    public function create(array $data): Role
    {
        return $this->repository->create($data);
    }

    public function findOrFail(int $id): Role
    {
        return $this->repository->findOrFail($id);
    }

    public function update(int $id, array $data): Role
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
