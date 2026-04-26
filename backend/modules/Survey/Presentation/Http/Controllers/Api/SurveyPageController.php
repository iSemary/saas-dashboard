<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Survey\Infrastructure\Persistence\SurveyPageRepositoryInterface;
use Modules\Survey\Infrastructure\Persistence\SurveyRepositoryInterface;

class SurveyPageController extends ApiController
{
    public function __construct(
        private SurveyPageRepositoryInterface $repository,
        private SurveyRepositoryInterface $surveyRepository,
    ) {}

    public function index(int $surveyId): JsonResponse
    {
        $pages = $this->repository->findBySurvey($surveyId);
        return $this->respondWithArray(['data' => $pages]);
    }

    public function store(Request $request, int $surveyId): JsonResponse
    {
        $survey = $this->surveyRepository->findOrFail($surveyId);
        $page = $survey->addPage($request->all());
        return $this->respondCreated(['data' => $page]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $page = $this->repository->update($id, $request->all());
        return $this->respondWithArray(['data' => $page]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->respondNoContent();
    }

    public function reorder(Request $request, int $surveyId): JsonResponse
    {
        $this->repository->reorder($surveyId, $request->get('ordered_ids', []));
        return $this->respondWithArray(['message' => translate('message.action_completed')]);
    }
}
