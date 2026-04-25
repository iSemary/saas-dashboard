<?php

declare(strict_types=1);

namespace Modules\CRM\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Infrastructure\Persistence\CrmAutomationRuleRepositoryInterface;

class CrmAutomationRuleApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly CrmAutomationRuleRepositoryInterface $rules) {}

    public function index(Request $request): JsonResponse
    {
        try {
            return $this->apiPaginated($this->rules->paginate([], (int) $request->get('per_page', 15)));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve rules', 500, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'event' => 'required|string|max:50',
                'conditions' => 'required|array',
                'actions' => 'required|array',
                'priority' => 'nullable|integer',
            ]);
            $data = $request->all();
            $data['created_by'] = auth()->id();
            $data['is_active'] = $request->boolean('is_active', true);
            $rule = $this->rules->create($data);
            return $this->apiSuccess($rule, 'Rule created', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create rule', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->rules->findOrFail($id));
        } catch (\Throwable $e) {
            return $this->apiError('Rule not found', 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $rule = $this->rules->update($id, $request->all());
            return $this->apiSuccess($rule, 'Rule updated');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to update rule', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->rules->delete($id);
            return $this->apiSuccess(null, 'Rule deleted');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete rule', 500, $e->getMessage());
        }
    }

    public function toggle(int $id): JsonResponse
    {
        try {
            $rule = $this->rules->findOrFail($id);
            $rule->update(['is_active' => !$rule->is_active]);
            return $this->apiSuccess($rule, 'Rule toggled');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to toggle rule', 500, $e->getMessage());
        }
    }
}
