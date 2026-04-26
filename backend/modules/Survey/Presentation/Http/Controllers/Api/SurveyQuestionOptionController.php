<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Survey\Infrastructure\Persistence\SurveyQuestionOptionRepositoryInterface;
use Modules\Survey\Infrastructure\Persistence\SurveyQuestionRepositoryInterface;

class SurveyQuestionOptionController extends ApiController
{
    public function __construct(
        private SurveyQuestionOptionRepositoryInterface $repository,
        private SurveyQuestionRepositoryInterface $questionRepository,
    ) {}

    public function index(int $questionId): JsonResponse
    {
        $options = $this->repository->findByQuestion($questionId);
        return $this->respondWithArray(['data' => $options]);
    }

    public function store(Request $request, int $questionId): JsonResponse
    {
        $question = $this->questionRepository->findOrFail($questionId);
        $data = [
            'question_id' => $questionId,
            'label' => $request->get('label'),
            'value' => $request->get('value'),
            'order' => $request->get('order'),
            'image_url' => $request->get('image_url'),
            'is_other' => $request->get('is_other', false),
            'point_value' => $request->get('point_value', 0),
        ];

        $option = $this->repository->create($data);
        return $this->respondCreated(['data' => $option]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $option = $this->repository->update($id, $request->all());
        return $this->respondWithArray(['data' => $option]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->respondNoContent();
    }

    public function reorder(Request $request, int $questionId): JsonResponse
    {
        $this->repository->reorder($questionId, $request->get('ordered_ids', []));
        return $this->respondWithArray(['message' => translate('message.action_completed')]);
    }
}
