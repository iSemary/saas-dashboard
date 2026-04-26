<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TimeManagement\Infrastructure\Persistence\TimeEntryRepositoryInterface;
use Modules\TimeManagement\Infrastructure\Persistence\TimesheetRepositoryInterface;
use Modules\TimeManagement\Infrastructure\Persistence\OvertimeRequestRepositoryInterface;

class TimeReportController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected TimeEntryRepositoryInterface $entryRepository,
        protected TimesheetRepositoryInterface $timesheetRepository,
        protected OvertimeRequestRepositoryInterface $overtimeRepository,
    ) {}

    public function utilization(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $from = $request->get('from', now()->subMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $data = $this->entryRepository->getUtilization($userId, $from, $to);

        $workingDays = 22;
        $expectedMinutes = $workingDays * 8 * 60;
        $totalMinutes = $data['total_minutes'];
        $billableMinutes = $data['billable_minutes'];

        return $this->apiSuccess([
            'total_minutes' => $totalMinutes,
            'billable_minutes' => $billableMinutes,
            'non_billable_minutes' => $data['non_billable_minutes'],
            'utilization_percent' => $expectedMinutes > 0 ? round(($totalMinutes / $expectedMinutes) * 100, 1) : 0,
            'billable_percent' => $totalMinutes > 0 ? round(($billableMinutes / $totalMinutes) * 100, 1) : 0,
        ]);
    }

    public function submittedHours(Request $request): JsonResponse
    {
        $from = $request->get('from', now()->subMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $timesheets = $this->timesheetRepository->getSubmittedHoursSummary($from, $to);

        return $this->apiSuccess($timesheets);
    }

    public function anomalies(Request $request): JsonResponse
    {
        $anomalies = $this->entryRepository->getAnomalies();

        return $this->apiSuccess($anomalies);
    }

    public function overtime(Request $request): JsonResponse
    {
        $from = $request->get('from', now()->subMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $overtime = $this->overtimeRepository->getOvertimeSummary($from, $to);

        return $this->apiSuccess($overtime);
    }

    public function billableRatio(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $from = $request->get('from', now()->subMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $data = $this->entryRepository->getBillableRatio($userId, $from, $to);
        $total = $data['billable'] + $data['non_billable'];

        return $this->apiSuccess([
            'billable' => $data['billable'],
            'non_billable' => $data['non_billable'],
            'ratio' => $total > 0 ? round($data['billable'] / $total, 2) : 0,
        ]);
    }
}
