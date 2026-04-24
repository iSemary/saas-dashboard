<?php

namespace Modules\Auth\Repositories\Tenant;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TenantLoginAttemptApiRepository implements TenantLoginAttemptApiRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $query = DB::table('login_attempts')->orderBy('created_at', 'desc');
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('ip', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }
        return $query->paginate($perPage);
    }
}
