<?php

namespace Modules\Subscription\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OnboardingApiController extends Controller
{
    use ApiResponseEnvelope;

    public function selectPlan(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|integer|exists:plans,id',
        ]);
        return $this->apiSuccess($validated, 'Plan selected successfully');
    }

    public function selectModules(Request $request)
    {
        $validated = $request->validate([
            'module_ids' => 'required|array',
            'module_ids.*' => 'integer|exists:modules,id',
        ]);
        return $this->apiSuccess($validated, 'Modules selected successfully');
    }
}
