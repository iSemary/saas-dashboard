<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Application\UseCases\Assets\AssignAssetUseCase;
use Modules\HR\Infrastructure\Persistence\AssetAssignmentRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\AssetCategoryRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\AssetRepositoryInterface;

class AssetApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected AssetCategoryRepositoryInterface $categoryRepository,
        protected AssetRepositoryInterface $assetRepository,
        protected AssetAssignmentRepositoryInterface $assignmentRepository,
        protected AssignAssetUseCase $assignAssetUseCase,
    ) {
        parent::__construct();
    }

    public function categories(Request $request): JsonResponse
    {
        return $this->success(data: $this->categoryRepository->paginate($request->integer('per_page', 15)));
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $category = $this->categoryRepository->create($request->only(['name']));
        return $this->success(data: $category, message: 'Asset category created successfully');
    }

    public function assets(Request $request): JsonResponse
    {
        return $this->success(data: $this->assetRepository->paginate($request->integer('per_page', 15)));
    }

    public function storeAsset(Request $request): JsonResponse
    {
        $asset = $this->assetRepository->create($request->only(['asset_tag', 'category_id', 'name', 'brand', 'model', 'serial_number', 'purchase_date', 'purchase_cost', 'status']));
        return $this->success(data: $asset, message: 'Asset created successfully');
    }

    public function assignments(Request $request): JsonResponse
    {
        return $this->success(data: $this->assignmentRepository->paginate($request->integer('per_page', 15)));
    }

    public function storeAssignment(Request $request): JsonResponse
    {
        $assignment = $this->assignAssetUseCase->execute($request->only(['asset_id', 'employee_id', 'assigned_at', 'notes']));
        return $this->success(data: $assignment, message: 'Asset assignment created successfully');
    }
}
