<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\EmailMarketing\Application\DTOs\Contact\CreateEmContactDTO;
use Modules\EmailMarketing\Application\DTOs\Contact\UpdateEmContactDTO;
use Modules\EmailMarketing\Application\UseCases\Contact\EmContactUseCase;
use Modules\EmailMarketing\Presentation\Http\Requests\StoreEmContactRequest;
use Modules\EmailMarketing\Presentation\Http\Requests\UpdateEmContactRequest;

class EmContactApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected EmContactUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->getTableList($request->getTableParams()));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(data: $this->useCase->find($id));
    }

    public function store(StoreEmContactRequest $request): JsonResponse
    {
        $dto = new CreateEmContactDTO(
            email: $request->input('email'),
            first_name: $request->input('first_name'),
            last_name: $request->input('last_name'),
            phone: $request->input('phone'),
            custom_fields: $request->input('custom_fields'),
            status: $request->input('status', 'active'),
        );
        return $this->success(data: $this->useCase->create($dto), message: translate('message.action_completed'));
    }

    public function update(UpdateEmContactRequest $request, int $id): JsonResponse
    {
        $dto = new UpdateEmContactDTO(
            email: $request->input('email'),
            first_name: $request->input('first_name'),
            last_name: $request->input('last_name'),
            phone: $request->input('phone'),
            custom_fields: $request->input('custom_fields'),
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
        return $this->success(data: ['deleted' => $count], message: "{$count} contacts deleted");
    }
}
