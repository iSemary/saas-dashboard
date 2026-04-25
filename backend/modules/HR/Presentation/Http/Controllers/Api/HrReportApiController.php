<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Modules\HR\Domain\Entities\Application;
use Modules\HR\Domain\Entities\Attendance;
use Modules\HR\Domain\Entities\Employee;
use Modules\HR\Domain\Entities\LeaveRequest;
use Modules\HR\Domain\Entities\Payroll;

class HrReportApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function headcount(): JsonResponse
    {
        return $this->success(data: [
            'total' => Employee::count(),
            'active' => Employee::where('employment_status', 'active')->count(),
            'terminated' => Employee::where('employment_status', 'terminated')->count(),
        ]);
    }

    public function leaveUsage(): JsonResponse
    {
        return $this->success(data: LeaveRequest::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get());
    }

    public function attendanceSummary(): JsonResponse
    {
        return $this->success(data: Attendance::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get());
    }

    public function payrollSummary(): JsonResponse
    {
        return $this->success(data: [
            'count' => Payroll::count(),
            'gross_total' => Payroll::sum('gross_pay'),
            'net_total' => Payroll::sum('net_pay'),
        ]);
    }

    public function recruitmentFunnel(): JsonResponse
    {
        return $this->success(data: Application::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get());
    }
}
