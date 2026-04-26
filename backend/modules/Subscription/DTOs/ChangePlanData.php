<?php

namespace Modules\Subscription\DTOs;

use Modules\Subscription\Http\Requests\Tenant\ChangePlanRequest;

readonly class ChangePlanData
{
    public function __construct(
        public int $new_plan_id,
        public ?bool $immediate = false,
        public ?bool $prorate = true,
        public ?string $success_url = null,
        public ?string $cancel_url = null,
    ) {}

    public static function fromRequest(ChangePlanRequest $request): self
    {
        return new self(...$request->validated());
    }
}
