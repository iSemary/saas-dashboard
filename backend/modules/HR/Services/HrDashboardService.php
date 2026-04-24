<?php

namespace Modules\HR\Services;

use Illuminate\Support\Facades\DB;

class HrDashboardService
{
    public function getDashboardData(): array
    {
        return [
            'employees_count' => DB::table('employees')->count(),
            'departments_count' => DB::table('departments')->count(),
            'leave_requests_count' => DB::table('leave_requests')->count(),
            'recent_employees' => DB::table('employees')->orderBy('created_at', 'desc')->limit(5)->get(),
        ];
    }
}
