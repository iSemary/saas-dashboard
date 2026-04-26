<?php

declare(strict_types=1);

namespace Modules\CRM\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Infrastructure\Persistence\AuditRepositoryInterface;

class CrmAuditApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected AuditRepositoryInterface $repository) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'auditable_type' => 'required|string',
                'auditable_id' => 'required|integer',
            ]);

            $perPage = (int) $request->get('per_page', 20);

            $audits = $this->repository->paginateByAuditable(
                $request->input('auditable_type'),
                (int) $request->input('auditable_id'),
                $perPage
            );

            return $this->apiPaginated($audits);
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }
}
