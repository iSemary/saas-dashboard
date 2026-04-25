<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Survey\Infrastructure\Persistence\SurveyAutomationRuleRepositoryInterface;

class SurveyAutomationRuleController extends ApiController
{
    public function __construct(
        private SurveyAutomationRuleRepositoryInterface $repository
    ) {}

    public function index(int $surveyId): JsonResponse
    {
        $rules = $this->repository->findBySurvey($surveyId);
        return $this->respondWithArray(['data' => $rules]);
    }

    public function store(Request $request, int $surveyId): JsonResponse
    {
        $data = [
            'survey_id' => $surveyId,
            'name' => $request->get('name'),
            'trigger_type' => $request->get('trigger_type'),
            'conditions' => $request->get('conditions', []),
            'action_type' => $request->get('action_type'),
            'action_config' => $request->get('action_config', []),
            'is_active' => $request->get('is_active', true),
            'created_by' => auth()->id(),
        ];

        $rule = $this->repository->create($data);
        return $this->respondCreated(['data' => $rule]);
    }

    public function show(int $id): JsonResponse
    {
        $rule = $this->repository->findOrFail($id);
        return $this->respondWithArray(['data' => $rule]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $rule = $this->repository->update($id, $request->all());
        return $this->respondWithArray(['data' => $rule]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->respondNoContent();
    }

    public function toggle(int $id): JsonResponse
    {
        $rule = $this->repository->findOrFail($id);
        $rule->toggle();
        return $this->respondWithArray(['data' => $rule->fresh()]);
    }
}
