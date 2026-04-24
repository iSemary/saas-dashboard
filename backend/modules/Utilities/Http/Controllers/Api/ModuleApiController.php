<?php

namespace Modules\Utilities\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Utilities\Services\ModuleService;

class ModuleApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected ModuleService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function toggle(Request $request, $id)
    {
        $module = $this->service->findOrFail($id);
        $module->is_active = !$module->is_active;
        $module->save();
        return $this->apiSuccess($module, 'Module toggled successfully');
    }
}
