<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use App\Http\Requests\TableListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProjectManagement\Infrastructure\Persistence\WorkspaceRepositoryInterface;
use Modules\ProjectManagement\Application\DTOs\CreateProjectData;

class WorkspaceController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected WorkspaceRepositoryInterface $repository,
    ) {}

    public function index(TableListRequest $request): JsonResponse
    {
        $params = $request->getTableParams();
        $result = $this->repository->getTableList($params);
        return $this->apiSuccess($result);
    }

    public function store(Request $request): JsonResponse
    {
        $item = $this->repository->create(array_merge($request->all(), ['created_by' => $request->user()->id]));
        return $this->apiSuccess($item, translate('message.created_successfully'), 201);
    }

    public function show(string $id): JsonResponse
    {
        $item = $this->repository->findOrFail($id);
        return $this->apiSuccess($item);
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
