<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TimeManagement\Infrastructure\Persistence\OvertimeRequestRepositoryInterface;

class OvertimeRequestController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected OvertimeRequestRepositoryInterface $repository) {}

    public function index(Request $request): JsonResponse
    {
        return $this->apiPaginated($this->repository->paginateByUser(
            $request->user()->id,
            $request->get('per_page', 15)
        ));
    }

    public function store(Request $request): JsonResponse
    {
        $item = $this->repository->create(array_merge($request->all(), [
            'user_id' => $request->user()->id,
            'tenant_id' => $request->user()->tenant_id ?? '',
            'status' => 'pending',
        ]));
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

    public function approve(Request $request, string $id): JsonResponse
    {
        $item = $this->repository->findOrFail($id);
        $item->approve($request->user()->id);
        return $this->apiSuccess($item->fresh(), translate('message.action_completed'));
    }

    public function reject(Request $request, string $id): JsonResponse
    {
        $item = $this->repository->findOrFail($id);
        $item->reject($request->user()->id, $request->input('reason', ''));
        return $this->apiSuccess($item->fresh(), translate('message.action_completed'));
    }
}
