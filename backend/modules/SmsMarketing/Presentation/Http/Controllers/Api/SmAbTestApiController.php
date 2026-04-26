<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\SmsMarketing\Application\DTOs\AbTest\CreateSmAbTestDTO;
use Modules\SmsMarketing\Application\DTOs\AbTest\UpdateSmAbTestDTO;
use Modules\SmsMarketing\Application\UseCases\AbTest\SmAbTestUseCase;
use Modules\SmsMarketing\Presentation\Http\Requests\StoreSmAbTestRequest;
use Modules\SmsMarketing\Presentation\Http\Requests\UpdateSmAbTestRequest;

class SmAbTestApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected SmAbTestUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->getTableList($request->getTableParams()));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(data: $this->useCase->find($id));
    }

    public function store(StoreSmAbTestRequest $request): JsonResponse
    {
        $dto = new CreateSmAbTestDTO(
            campaign_id: $request->integer('campaign_id'),
            variant_name: $request->input('variant_name'),
            body: $request->input('body'),
            percentage: $request->integer('percentage', 50),
        );
        return $this->success(data: $this->useCase->create($dto), message: translate('message.action_completed'));
    }

    public function update(UpdateSmAbTestRequest $request, int $id): JsonResponse
    {
        $dto = new UpdateSmAbTestDTO(
            variant_name: $request->input('variant_name'),
            body: $request->input('body'),
            percentage: $request->has('percentage') ? $request->integer('percentage') : null,
            winner: $request->input('winner'),
        );
        $this->useCase->update($id, $dto);
        return $this->success(data: $this->useCase->find($id), message: translate('message.action_completed'));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->useCase->delete($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function selectWinner(Request $request, int $id): JsonResponse
    {
        $this->useCase->selectWinner($id, $request->input('variant'));
        return $this->success(message: translate('message.action_completed'));
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $count = $this->useCase->bulkDelete($request->input('ids', []));
        return $this->success(data: ['deleted' => $count], message: "{$count} A/B tests deleted");
    }
}
