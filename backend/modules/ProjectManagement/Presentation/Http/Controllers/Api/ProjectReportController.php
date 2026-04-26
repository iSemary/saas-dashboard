<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProjectManagement\Infrastructure\Persistence\ProjectRepositoryInterface;
use Modules\ProjectManagement\Infrastructure\Persistence\TaskRepositoryInterface;

class ProjectReportController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected ProjectRepositoryInterface $projectRepository,
        protected TaskRepositoryInterface $taskRepository,
    ) {}

    public function throughput(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $from = $request->get('from', now()->subMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $completed = $this->taskRepository->count([
            'status' => 'done',
            'tenant_id' => $tenantId,
            'from' => $from,
            'to' => $to,
        ]);

        return $this->apiSuccess(['completed_tasks' => $completed, 'from' => $from, 'to' => $to]);
    }

    public function overdue(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $overdue = $this->taskRepository->list([
            'overdue' => true,
            'tenant_id' => $tenantId,
        ]);

        return $this->apiSuccess($overdue);
    }

    public function workload(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $workload = $this->taskRepository->getWorkloadByAssignee($tenantId);

        return $this->apiSuccess($workload);
    }

    public function health(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $projects = $this->projectRepository->getHealthDistribution($tenantId);

        return $this->apiSuccess($projects);
    }
}
