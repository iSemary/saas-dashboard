<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Survey\Infrastructure\Persistence\SurveyShareRepositoryInterface;
use Modules\Survey\Infrastructure\Persistence\SurveyRepositoryInterface;

class SurveyShareController extends ApiController
{
    public function __construct(
        private SurveyShareRepositoryInterface $repository,
        private SurveyRepositoryInterface $surveyRepository,
    ) {}

    public function index(int $surveyId): JsonResponse
    {
        $shares = $this->repository->findBySurvey($surveyId);
        return $this->respondWithArray(['data' => $shares]);
    }

    public function store(Request $request, int $surveyId): JsonResponse
    {
        $survey = $this->surveyRepository->findOrFail($surveyId);

        $data = [
            'survey_id' => $surveyId,
            'channel' => $request->get('channel'),
            'config' => $request->get('config', []),
            'max_uses' => $request->get('max_uses'),
            'expires_at' => $request->get('expires_at'),
            'created_by' => auth()->id(),
        ];

        $share = $this->repository->create($data);

        return $this->respondCreated([
            'data' => $share,
            'public_url' => $share->getPublicUrl(),
            'embed_code' => $share->getEmbedCode(),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->respondNoContent();
    }
}
