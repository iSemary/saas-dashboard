<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\SmsMarketing\Application\DTOs\Contact\CreateSmContactDTO;
use Modules\SmsMarketing\Application\DTOs\Contact\UpdateSmContactDTO;
use Modules\SmsMarketing\Application\UseCases\Contact\SmContactUseCase;
use Modules\SmsMarketing\Presentation\Http\Requests\StoreSmContactRequest;
use Modules\SmsMarketing\Presentation\Http\Requests\UpdateSmContactRequest;

class SmContactApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected SmContactUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->getTableList($request->getTableParams()));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(data: $this->useCase->find($id));
    }

    public function store(StoreSmContactRequest $request): JsonResponse
    {
        $dto = new CreateSmContactDTO(
            phone: $request->input('phone'),
            first_name: $request->input('first_name'),
            last_name: $request->input('last_name'),
            email: $request->input('email'),
            custom_fields: $request->input('custom_fields'),
            status: $request->input('status', 'active'),
        );
        return $this->success(data: $this->useCase->create($dto), message: translate('message.action_completed'));
    }

    public function update(UpdateSmContactRequest $request, int $id): JsonResponse
    {
        $dto = new UpdateSmContactDTO(
            phone: $request->input('phone'),
            first_name: $request->input('first_name'),
            last_name: $request->input('last_name'),
            email: $request->input('email'),
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
