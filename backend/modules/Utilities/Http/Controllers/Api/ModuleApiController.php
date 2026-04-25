<?php

namespace Modules\Utilities\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Utilities\Services\ModuleService;

class ModuleApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected ModuleService $service)
    {
        $this->middleware('auth:api');
        $this->middleware('landlord_roles');
        $this->middleware('throttle:60,1');
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function show(int $id)
    {
        return $this->apiSuccess($this->service->findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'module_key' => 'required|string|unique:modules,module_key',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable',
            'slogan' => 'nullable|string|max:255',
            'navigation' => 'nullable|array',
            'navigation.*.key' => 'required_with:navigation|string',
            'navigation.*.label' => 'required_with:navigation|string',
            'navigation.*.route' => 'required_with:navigation|string',
            'navigation.*.icon' => 'required_with:navigation|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        return $this->apiSuccess($this->service->create($data), 'Module created successfully');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'module_key' => "sometimes|string|unique:modules,module_key,{$id}",
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable',
            'slogan' => 'nullable|string|max:255',
            'navigation' => 'nullable|array',
            'navigation.*.key' => 'required_with:navigation|string',
            'navigation.*.label' => 'required_with:navigation|string',
            'navigation.*.route' => 'required_with:navigation|string',
            'navigation.*.icon' => 'required_with:navigation|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        return $this->apiSuccess($this->service->update($id, $data), 'Module updated successfully');
    }

    public function toggle(Request $request, $id)
    {
        $module = $this->service->findOrFail($id);
        $module->status = $module->status === 'active' ? 'inactive' : 'active';
        $module->save();
        return $this->apiSuccess($module, 'Module toggled successfully');
    }
}
