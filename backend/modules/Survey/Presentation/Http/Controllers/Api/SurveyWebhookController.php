<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Survey\Infrastructure\Persistence\SurveyWebhookRepositoryInterface;

class SurveyWebhookController extends ApiController
{
    public function __construct(
        private SurveyWebhookRepositoryInterface $repository
    ) {}

    public function index(int $surveyId): JsonResponse
    {
        $webhooks = $this->repository->findBySurvey($surveyId);
        return $this->respondWithArray(['data' => $webhooks]);
    }

    public function store(Request $request, int $surveyId): JsonResponse
    {
        $data = [
            'survey_id' => $surveyId,
            'name' => $request->get('name'),
            'url' => $request->get('url'),
            'events' => $request->get('events', []),
            'is_active' => $request->get('is_active', true),
            'created_by' => auth()->id(),
        ];

        $webhook = $this->repository->create($data);

        return $this->respondCreated([
            'data' => $webhook,
            'secret' => $webhook->secret, // Show secret once on creation
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $webhook = $this->repository->findOrFail($id);
        return $this->respondWithArray(['data' => $webhook]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $webhook = $this->repository->update($id, $request->all());
        return $this->respondWithArray(['data' => $webhook]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->respondNoContent();
    }

    public function toggle(int $id): JsonResponse
    {
        $webhook = $this->repository->findOrFail($id);
        $webhook->toggle();
        return $this->respondWithArray(['data' => $webhook->fresh()]);
    }

    public function regenerateSecret(int $id): JsonResponse
    {
        $webhook = $this->repository->findOrFail($id);
        $webhook->regenerateSecret();
        return $this->respondWithArray([
            'data' => $webhook->fresh(),
            'secret' => $webhook->fresh()->secret, // Show new secret once
        ]);
    }
}
