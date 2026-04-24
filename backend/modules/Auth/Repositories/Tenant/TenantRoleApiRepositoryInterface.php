<?php

namespace Modules\Auth\Repositories\Tenant;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

interface TenantRoleRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator;

    public function create(array $data): Role;

    public function findOrFail(int $id): Role;

    public function update(int $id, array $data): Role;

    public function delete(int $id): bool;
}
