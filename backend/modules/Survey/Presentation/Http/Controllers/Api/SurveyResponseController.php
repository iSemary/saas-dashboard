<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Survey\Infrastructure\Persistence\SurveyResponseRepositoryInterface;
use Modules\Survey\Infrastructure\Persistence\SurveyRepositoryInterface;

class SurveyResponseController extends ApiController
{
    public function __construct(
        private SurveyResponseRepositoryInterface $repository,
        private SurveyRepositoryInterface $surveyRepository,
    ) {}

    public function index(int $surveyId, Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'respondent_email']);
        $responses = $this->repository->paginateBySurvey($surveyId, $filters, $request->get('per_page', 15));
        return $this->respondWithArray(['data' => $responses]);
    }

    public function show(int $id): JsonResponse
    {
        $response = $this->repository->findOrFail($id);
        return $this->respondWithArray(['data' => $response->load(['answers.question', 'survey'])]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->respondNoContent();
    }

    public function analytics(int $surveyId): JsonResponse
    {
        $survey = $this->surveyRepository->findOrFail($surveyId);
        $statusCounts = $this->repository->countByStatus($surveyId);

        return $this->respondWithArray([
            'survey_id' => $surveyId,
            'total_responses' => $survey->getTotalResponses(),
            'completed_responses' => $survey->getCompletedResponses(),
            'completion_rate' => $survey->getCompletionRate(),
            'status_counts' => $statusCounts,
        ]);
    }
}
