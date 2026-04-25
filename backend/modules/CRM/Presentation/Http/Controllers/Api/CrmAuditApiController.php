<?php

declare(strict_types=1);

namespace Modules\CRM\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OwenIt\Auditing\Models\Audit;

class CrmAuditApiController extends Controller
{
    use ApiResponseEnvelope;

    public function index(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'auditable_type' => 'required|string',
                'auditable_id' => 'required|integer',
            ]);

            $perPage = (int) $request->get('per_page', 20);

            $audits = Audit::where('auditable_type', $request->input('auditable_type'))
                ->where('auditable_id', $request->input('auditable_id'))
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return $this->apiPaginated($audits);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve audit log', 500, $e->getMessage());
        }
    }
}
