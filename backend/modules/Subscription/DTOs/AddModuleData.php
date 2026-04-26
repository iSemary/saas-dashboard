<?php

namespace Modules\Subscription\DTOs;

use Modules\Subscription\Http\Requests\Tenant\AddModuleRequest;

readonly class AddModuleData
{
    public function __construct(
        public int $module_id,
        public ?string $billing_cycle = 'monthly',
        public ?bool $immediate = true,
        public ?array $metadata = [],
    ) {}

    public static function fromRequest(AddModuleRequest $request): self
    {
        return new self(...$request->validated());
    }
}
