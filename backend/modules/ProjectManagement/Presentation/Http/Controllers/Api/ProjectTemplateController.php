<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProjectManagement\Infrastructure\Persistence\ProjectTemplateRepositoryInterface;

class ProjectTemplateController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected ProjectTemplateRepositoryInterface $repository) {}

    public function index(Request $request): JsonResponse
    {
        return $this->apiSuccess($this->repository->all());
    }

    public function store(Request $request): JsonResponse
    {
        $item = $this->repository->create(array_merge($request->all(), ['created_by' => $request->user()->id]));
        return $this->apiSuccess($item, translate('message.created_successfully'), 201);
    }

    public function show(string $id): JsonResponse
    {
        return $this->apiSuccess($this->repository->findOrFail($id));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $item = $this->repository->update($id, $request->all());
        return $this->apiSuccess($item, translate('message.updated_successfully'));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }
}
