<?php

namespace Modules\Subscription\DTOs;

use Modules\Subscription\Http\Requests\Tenant\RemoveModuleRequest;

readonly class RemoveModuleData
{
    public function __construct(
        public int $module_id,
        public ?bool $immediate = false,
        public ?bool $refund = false,
        public ?string $reason = null,
    ) {}

    public static function fromRequest(RemoveModuleRequest $request): self
    {
        return new self(...$request->validated());
    }
}
