<?php

namespace Modules\Auth\Services\Tenant;

use Modules\Auth\Repositories\Tenant\TenantLoginAttemptApiRepositoryInterface;

class TenantLoginAttemptApiService
{
    public function __construct(protected TenantLoginAttemptApiRepositoryInterface $repository) {}

    public function list(array $filters = [], int $perPage = 50)
    {
        return $this->repository->paginate($filters, $perPage);
    }
}
