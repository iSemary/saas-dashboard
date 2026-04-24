<?php

namespace Modules\Development\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Development\Services\ModuleEntityService;

class ModuleEntityApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected ModuleEntityService $service) {}

    public function index()
    {
        return $this->apiSuccess($this->service->list());
    }

    public function map()
    {
        return $this->apiSuccess($this->service->getMap());
    }

    public function sync(Request $request)
    {
        $validated = $request->validate([
            'entities' => 'required|array',
        ]);
        $this->service->sync($validated['entities']);
        return $this->apiSuccess(null, 'Entities synced successfully');
    }
}
