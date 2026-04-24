<?php

namespace Modules\Auth\Repositories\Tenant;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Activitylog\Models\Activity;

interface TenantActivityLogApiRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator;
}
