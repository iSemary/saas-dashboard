<?php

namespace Modules\Utilities\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Utilities\Services\ReleaseService;

class ReleaseApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected ReleaseService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'version' => 'required|string|max:50',
            'title' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);
        return $this->apiSuccess($this->service->create($validated), translate('message.created_successfully'), 201);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }
}
