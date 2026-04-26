<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\EmailMarketing\Application\UseCases\SendingLog\EmSendingLogUseCase;

class EmSendingLogApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected EmSendingLogUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->getTableList($request->getTableParams()));
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(data: $this->useCase->find($id));
    }
}
