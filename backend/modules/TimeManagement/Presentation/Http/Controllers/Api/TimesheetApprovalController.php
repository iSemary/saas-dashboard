<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TimeManagement\Domain\ValueObjects\TimesheetStatus;
use Modules\TimeManagement\Infrastructure\Persistence\TimesheetRepositoryInterface;

class TimesheetApprovalController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TimesheetRepositoryInterface $repository) {}

    public function index(Request $request): JsonResponse
    {
        $pending = $this->repository->paginate([
            'status' => 'submitted',
            'tenant_id' => $request->user()->tenant_id,
        ], $request->get('per_page', 15));

        return $this->apiPaginated($pending);
    }

    public function store(Request $request): JsonResponse
    {
        $ids = $request->input('timesheet_ids', []);
        $action = $request->input('action', 'approve');
        $count = 0;

        foreach ($ids as $id) {
            $ts = $this->repository->find($id);
            if ($ts && $ts->status === 'submitted') {
                if ($action === 'approve') {
                    $ts->transitionStatus(TimesheetStatus::Approved, $request->user()->id);
                } else {
                    $ts->transitionStatus(TimesheetStatus::Rejected, $request->user()->id, $request->input('reason', ''));
                }
                $count++;
            }
        }

        return $this->apiSuccess(['processed' => $count], "{$count} timesheets {$action}d");
    }
}
