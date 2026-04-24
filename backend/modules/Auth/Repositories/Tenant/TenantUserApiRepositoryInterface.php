<?php

namespace Modules\Auth\Repositories\Tenant;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TenantUserApiRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator;

    public function create(array $data): User;

    public function findOrFail(int $id): User;

    public function update(int $id, array $data): User;

    public function delete(int $id): bool;

    public function assignRoles(int $userId, array $roleIds): User;
}
