<?php

namespace Modules\Utilities\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Utilities\Services\TagService;

class TagApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TagService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
        ]);
        return $this->apiSuccess($this->service->create($validated), 'Tag created successfully', 201);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Tag deleted successfully');
    }
}
