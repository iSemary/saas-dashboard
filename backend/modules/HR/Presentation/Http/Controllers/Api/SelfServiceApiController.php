<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Domain\Entities\AssetAssignment;
use Modules\HR\Domain\Entities\Attendance;
use Modules\HR\Domain\Entities\Employee;
use Modules\HR\Domain\Entities\Goal;
use Modules\HR\Domain\Entities\LeaveRequest;
use Modules\HR\Domain\Entities\Payroll;

class SelfServiceApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    private function employee(Request $request): Employee
    {
        return $request->attributes->get('employee_context')
            ?? Employee::query()->where('user_id', auth()->id())->firstOrFail();
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(data: $this->employee($request));
    }

    public function leaves(Request $request): JsonResponse
    {
        $employee = $this->employee($request);
        return $this->success(data: LeaveRequest::query()->where('employee_id', $employee->id)->latest()->paginate(15));
    }

    public function attendance(Request $request): JsonResponse
    {
        $employee = $this->employee($request);
        return $this->success(data: Attendance::query()->where('employee_id', $employee->id)->latest('date')->paginate(31));
    }

    public function payroll(Request $request): JsonResponse
    {
        $employee = $this->employee($request);
        return $this->success(data: Payroll::query()->where('employee_id', $employee->id)->latest('pay_date')->paginate(12));
    }

    public function goals(Request $request): JsonResponse
    {
        $employee = $this->employee($request);
        return $this->success(data: Goal::query()->where('employee_id', $employee->id)->latest()->paginate(20));
    }

    public function assets(Request $request): JsonResponse
    {
        $employee = $this->employee($request);
        return $this->success(data: AssetAssignment::query()->where('employee_id', $employee->id)->latest()->paginate(20));
    }
}
