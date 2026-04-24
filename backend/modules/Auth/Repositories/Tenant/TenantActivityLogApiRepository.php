<?php

namespace Modules\Auth\Repositories\Tenant;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Activitylog\Models\Activity;

class TenantActivityLogApiRepository implements TenantActivityLogApiRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $query = Activity::with('causer');
        if (isset($filters['search'])) {
            $query->where('description', 'like', "%{$filters['search']}%");
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
