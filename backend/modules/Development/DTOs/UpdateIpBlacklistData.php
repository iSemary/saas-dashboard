<?php

namespace Modules\Development\DTOs;

use Modules\Development\Http\Requests\UpdateIpBlacklistRequest;

readonly class UpdateIpBlacklistData
{
    public function __construct(
        public ?string $ip_address = null,
    ) {}

    public static function fromRequest(UpdateIpBlacklistRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'ip_address' => $this->ip_address,
        ], fn ($value) => $value !== null);
    }
}
