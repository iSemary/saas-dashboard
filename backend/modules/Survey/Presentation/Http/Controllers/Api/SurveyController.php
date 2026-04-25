<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Survey\Application\DTOs\CreateSurveyData;
use Modules\Survey\Application\DTOs\UpdateSurveyData;
use Modules\Survey\Application\UseCases\CreateSurvey;
use Modules\Survey\Application\UseCases\PublishSurvey;
use Modules\Survey\Infrastructure\Persistence\SurveyRepositoryInterface;

class SurveyController extends ApiController
{
    public function __construct(
        private SurveyRepositoryInterface $repository,
        private CreateSurvey $createSurvey,
        private PublishSurvey $publishSurvey,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'search']);
        $surveys = $this->repository->paginate($filters, $request->get('per_page', 15));
        return $this->respondWithArray(['data' => $surveys]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = CreateSurveyData::fromArray($request->all());
        $survey = $this->createSurvey->execute($data, auth()->id());
        return $this->respondCreated(['data' => $survey]);
    }

    public function show(int $id): JsonResponse
    {
        $survey = $this->repository->findOrFail($id);
        return $this->respondWithArray(['data' => $survey->load(['pages.questions.options', 'theme'])]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = UpdateSurveyData::fromArray($request->all());
        $survey = $this->repository->update($id, $data->toArray());
        return $this->respondWithArray(['data' => $survey]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->respondNoContent();
    }

    public function duplicate(int $id): JsonResponse
    {
        $survey = $this->repository->findOrFail($id);
        $newSurvey = $survey->duplicate();
        return $this->respondCreated(['data' => $newSurvey]);
    }

    public function publish(int $id): JsonResponse
    {
        $survey = $this->publishSurvey->execute($id);
        return $this->respondWithArray(['data' => $survey]);
    }

    public function close(int $id): JsonResponse
    {
        $survey = $this->repository->findOrFail($id);
        $survey->close();
        return $this->respondWithArray(['data' => $survey->fresh()]);
    }

    public function pause(int $id): JsonResponse
    {
        $survey = $this->repository->findOrFail($id);
        $survey->pause();
        return $this->respondWithArray(['data' => $survey->fresh()]);
    }

    public function resume(int $id): JsonResponse
    {
        $survey = $this->repository->findOrFail($id);
        $survey->resume();
        return $this->respondWithArray(['data' => $survey->fresh()]);
    }
}
