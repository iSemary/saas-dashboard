<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Survey\Infrastructure\Persistence\SurveyThemeRepositoryInterface;

class SurveyThemeController extends ApiController
{
    public function __construct(
        private SurveyThemeRepositoryInterface $repository
    ) {}

    public function index(): JsonResponse
    {
        $themes = $this->repository->list();
        return $this->respondWithArray(['data' => $themes]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $data['created_by'] = auth()->id();
        $theme = $this->repository->create($data);
        return $this->respondCreated(['data' => $theme]);
    }

    public function show(int $id): JsonResponse
    {
        $theme = $this->repository->findOrFail($id);
        return $this->respondWithArray(['data' => $theme]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $theme = $this->repository->update($id, $request->all());
        return $this->respondWithArray(['data' => $theme]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->respondNoContent();
    }
}
