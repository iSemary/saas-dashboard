<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Survey\Application\DTOs\CreateSurveyQuestionData;
use Modules\Survey\Application\UseCases\CreateSurveyQuestion;
use Modules\Survey\Infrastructure\Persistence\SurveyQuestionRepositoryInterface;

class SurveyQuestionController extends ApiController
{
    public function __construct(
        private SurveyQuestionRepositoryInterface $repository,
        private CreateSurveyQuestion $createQuestion,
    ) {}

    public function index(int $surveyId): JsonResponse
    {
        $questions = $this->repository->findBySurvey($surveyId);
        return $this->respondWithArray(['data' => $questions]);
    }

    public function store(Request $request, int $surveyId): JsonResponse
    {
        $data = CreateSurveyQuestionData::fromArray($request->all());
        $question = $this->createQuestion->execute($data);
        return $this->respondCreated(['data' => $question]);
    }

    public function show(int $id): JsonResponse
    {
        $question = $this->repository->findOrFail($id);
        return $this->respondWithArray(['data' => $question->load('options')]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $question = $this->repository->update($id, $request->all());
        return $this->respondWithArray(['data' => $question]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->respondNoContent();
    }

    public function reorder(Request $request, int $surveyId): JsonResponse
    {
        $this->repository->reorder($surveyId, $request->get('ordered_ids', []));
        return $this->respondWithArray(['message' => 'Questions reordered']);
    }
}
