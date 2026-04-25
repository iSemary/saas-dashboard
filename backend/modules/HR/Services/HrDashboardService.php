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
            'department_distribution' => $this->getDepartmentDistribution(),
            'attendance_trends' => $this->getAttendanceTrends(),
            'leave_types' => $this->getLeaveTypes(),
        ];
    }

    private function getDepartmentDistribution(): array
    {
        return DB::table('employees')
            ->select('department', DB::raw('COUNT(*) as count'))
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->groupBy('department')
            ->get()
            ->map(fn ($row) => [
                'department' => $row->department,
                'count' => $row->count,
            ])
            ->toArray();
    }

    private function getAttendanceTrends(int $days = 14): array
    {
        return DB::table('attendances')
            ->select(
                DB::raw('DATE(date) as date'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late')
            )
            ->where('date', '>=', now()->subDays($days)->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'present' => (int) $row->present,
                'absent' => (int) $row->absent,
                'late' => (int) $row->late,
            ])
            ->toArray();
    }

    private function getLeaveTypes(): array
    {
        return DB::table('leave_requests')
            ->select('leave_type', DB::raw('COUNT(*) as count'))
            ->groupBy('leave_type')
            ->get()
            ->map(fn ($row) => [
                'leave_type' => $row->leave_type,
                'count' => $row->count,
            ])
            ->toArray();
    }
}
