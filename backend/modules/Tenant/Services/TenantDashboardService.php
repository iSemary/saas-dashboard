<?php

namespace Modules\Tenant\Services;

use Illuminate\Support\Facades\DB;

class TenantDashboardService
{
    public function getStats(): array
    {
        return [
            'users_count' => DB::table('users')->count(),
            'roles_count' => DB::table('roles')->count(),
            'tickets_open' => DB::table('tickets')->where('status', 'open')->count(),
            'tickets_closed' => DB::table('tickets')->where('status', 'closed')->count(),
            'brands_count' => DB::table('brands')->count(),
            'branches_count' => DB::table('branches')->count(),
        ];
    }
}
