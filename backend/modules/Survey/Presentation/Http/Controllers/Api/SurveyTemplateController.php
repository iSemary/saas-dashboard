<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Survey\Infrastructure\Persistence\SurveyTemplateRepositoryInterface;

class SurveyTemplateController extends ApiController
{
    public function __construct(
        private SurveyTemplateRepositoryInterface $repository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $templates = $this->repository->list($request->only(['category', 'search']));
        return $this->respondWithArray(['data' => $templates]);
    }

    public function show(int $id): JsonResponse
    {
        $template = $this->repository->findOrFail($id);
        return $this->respondWithArray(['data' => $template]);
    }

    public function createSurvey(int $id): JsonResponse
    {
        $template = $this->repository->findOrFail($id);
        $survey = $template->createSurveyFromTemplate(auth()->id());
        return $this->respondCreated(['data' => $survey]);
    }
}
