<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProjectManagement\Infrastructure\Persistence\ProjectRepositoryInterface;

class ProjectManagementDashboardController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected ProjectRepositoryInterface $repository) {}

    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id ?? null;

        return $this->apiSuccess([
            'total_projects' => $this->repository->count(['tenant_id' => $tenantId]),
            'active_projects' => $this->repository->count(['tenant_id' => $tenantId, 'status' => 'active']),
            'completed_projects' => $this->repository->count(['tenant_id' => $tenantId, 'status' => 'completed']),
            'on_hold_projects' => $this->repository->count(['tenant_id' => $tenantId, 'status' => 'on_hold']),
            'overdue_tasks' => 0,
            'recent_projects' => $this->repository->list(['tenant_id' => $tenantId, 'limit' => 5]),
        ]);
    }
}
