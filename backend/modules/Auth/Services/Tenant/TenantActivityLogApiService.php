<?php

namespace Modules\Auth\Services\Tenant;

use Modules\Auth\Repositories\Tenant\TenantActivityLogApiRepositoryInterface;

class TenantActivityLogApiService
{
    public function __construct(protected TenantActivityLogApiRepositoryInterface $repository) {}

    public function list(array $filters = [], int $perPage = 50)
    {
        return $this->repository->paginate($filters, $perPage);
    }
}
