<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\SmsMarketing\Application\DTOs\Template\CreateSmTemplateDTO;
use Modules\SmsMarketing\Application\DTOs\Template\UpdateSmTemplateDTO;
use Modules\SmsMarketing\Application\UseCases\Template\SmTemplateUseCase;
use Modules\SmsMarketing\Presentation\Http\Requests\StoreSmTemplateRequest;
use Modules\SmsMarketing\Presentation\Http\Requests\UpdateSmTemplateRequest;

class SmTemplateApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected SmTemplateUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->getTableList($request->getTableParams()));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(data: $this->useCase->find($id));
    }

    public function store(StoreSmTemplateRequest $request): JsonResponse
    {
        $dto = new CreateSmTemplateDTO(
            name: $request->input('name'),
            body: $request->input('body'),
            variables: $request->input('variables'),
            status: $request->input('status', 'draft'),
        );
        return $this->success(data: $this->useCase->create($dto), message: translate('message.action_completed'));
    }

    public function update(UpdateSmTemplateRequest $request, int $id): JsonResponse
    {
        $dto = new UpdateSmTemplateDTO(
            name: $request->input('name'),
            body: $request->input('body'),
            variables: $request->input('variables'),
            status: $request->input('status'),
        );
        $this->useCase->update($id, $dto);
        return $this->success(data: $this->useCase->find($id), message: translate('message.action_completed'));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->useCase->delete($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $count = $this->useCase->bulkDelete($request->input('ids', []));
        return $this->success(data: ['deleted' => $count], message: "{$count} templates deleted");
    }
}
