<?php

declare(strict_types=1);

namespace Modules\CRM\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Infrastructure\Persistence\CrmPipelineStageRepositoryInterface;

class CrmPipelineStageApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly CrmPipelineStageRepositoryInterface $stages) {}

    public function index(): JsonResponse
    {
        try {
            return $this->apiSuccess($this->stages->getOrdered());
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve stages', 500, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'key' => 'required|string|max:50|unique:crm_pipeline_stages',
                'probability' => 'required|integer|min:0|max:100',
                'color' => 'nullable|string|max:20',
                'order' => 'nullable|integer',
            ]);
            $data = $request->all();
            $data['is_default'] = $request->boolean('is_default', false);
            $stage = $this->stages->create($data);
            return $this->apiSuccess($stage, 'Stage created', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create stage', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->stages->findOrFail($id));
        } catch (\Throwable $e) {
            return $this->apiError('Stage not found', 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $stage = $this->stages->update($id, $request->all());
            return $this->apiSuccess($stage, 'Stage updated');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to update stage', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->stages->delete($id);
            return $this->apiSuccess(null, 'Stage deleted');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete stage', 500, $e->getMessage());
        }
    }

    public function reorder(Request $request): JsonResponse
    {
        try {
            $request->validate(['stages' => 'required|array', 'stages.*.id' => 'required|integer', 'stages.*.order' => 'required|integer']);
            foreach ($request->input('stages') as $stageData) {
                $this->stages->update($stageData['id'], ['order' => $stageData['order']]);
            }
            return $this->apiSuccess(null, 'Stages reordered');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to reorder stages', 500, $e->getMessage());
        }
    }
}
