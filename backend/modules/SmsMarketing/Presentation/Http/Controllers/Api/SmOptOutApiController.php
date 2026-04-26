<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TableListRequest;
use Modules\SmsMarketing\Application\DTOs\OptOut\CreateSmOptOutDTO;
use Modules\SmsMarketing\Application\UseCases\OptOut\SmOptOutUseCase;
use Modules\SmsMarketing\Presentation\Http\Requests\StoreSmOptOutRequest;

class SmOptOutApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(protected SmOptOutUseCase $useCase) { parent::__construct(); }

    public function index(TableListRequest $request): JsonResponse
    {
        return $this->success(data: $this->useCase->list($request->all(), $request->integer('per_page', 15)));
    }

    public function store(StoreSmOptOutRequest $request): JsonResponse
    {
        $dto = new CreateSmOptOutDTO(
            contact_id: $request->integer('contact_id'),
            campaign_id: $request->input('campaign_id'),
            reason: $request->input('reason'),
        );
        $this->useCase->optOut($dto);
        return $this->success(message: translate('message.action_completed'));
    }
}
