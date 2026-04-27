<?php

namespace Modules\Auth\Repositories\Tenant;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use OwenIt\Auditing\Models\Audit;

class TenantActivityLogApiRepository implements TenantActivityLogApiRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $query = Audit::with('user');
        if (isset($filters['search'])) {
            $query->where('event', 'like', "%{$filters['search']}%")
                  ->orWhere('auditable_type', 'like', "%{$filters['search']}%");
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
