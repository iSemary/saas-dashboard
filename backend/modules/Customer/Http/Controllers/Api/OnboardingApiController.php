<?php

namespace Modules\Customer\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Customer\Services\BrandService;

class OnboardingApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected BrandService $brandService) {}

    public function createBrand(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'domain' => 'nullable|string|max:255',
        ]);
        $brand = $this->brandService->create($validated);
        return $this->apiSuccess($brand, 'Brand created successfully', 201);
    }
}
