<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\EmailMarketing\Application\DTOs\AbTest\CreateEmAbTestDTO;
use Modules\EmailMarketing\Application\DTOs\AbTest\UpdateEmAbTestDTO;
use Modules\EmailMarketing\Application\UseCases\AbTest\EmAbTestUseCase;
use Modules\EmailMarketing\Presentation\Http\Requests\StoreEmAbTestRequest;
use Modules\EmailMarketing\Presentation\Http\Requests\UpdateEmAbTestRequest;

class EmAbTestApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected EmAbTestUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->getTableList($request->getTableParams()));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(data: $this->useCase->find($id));
    }

    public function store(StoreEmAbTestRequest $request): JsonResponse
    {
        $dto = new CreateEmAbTestDTO(
            campaign_id: $request->integer('campaign_id'),
            variant_name: $request->input('variant_name'),
            subject: $request->input('subject'),
            body_html: $request->input('body_html'),
            percentage: $request->integer('percentage', 50),
        );
        return $this->success(data: $this->useCase->create($dto), message: translate('message.action_completed'));
    }

    public function update(UpdateEmAbTestRequest $request, int $id): JsonResponse
    {
        $dto = new UpdateEmAbTestDTO(
            variant_name: $request->input('variant_name'),
            subject: $request->input('subject'),
            body_html: $request->input('body_html'),
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
