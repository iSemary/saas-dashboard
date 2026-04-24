<?php

namespace Modules\Auth\Repositories\Tenant;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;

interface TenantPermissionApiRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator;

    public function create(array $data): Permission;

    public function findOrFail(int $id): Permission;

    public function update(int $id, array $data): Permission;

    public function delete(int $id): bool;
}
