<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\SmsMarketing\Application\DTOs\ImportJob\CreateSmImportJobDTO;
use Modules\SmsMarketing\Application\UseCases\ImportJob\SmImportJobUseCase;
use Modules\SmsMarketing\Presentation\Http\Requests\StoreSmImportJobRequest;

class SmImportJobApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected SmImportJobUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->getTableList($request->getTableParams()));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(data: $this->useCase->find($id));
    }

    public function store(StoreSmImportJobRequest $request): JsonResponse
    {
        $dto = new CreateSmImportJobDTO(
            contact_list_id: $request->integer('contact_list_id'),
            file_path: $request->input('file_path'),
            column_mapping: $request->input('column_mapping'),
        );
        return $this->success(data: $this->useCase->create($dto), message: translate('message.action_completed'));
    }

    public function process(int $id): JsonResponse
    {
        $this->useCase->process($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->useCase->delete($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $count = $this->useCase->bulkDelete($request->input('ids', []));
        return $this->success(data: ['deleted' => $count], message: "{$count} import jobs deleted");
    }
}
