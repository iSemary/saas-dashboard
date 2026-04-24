<?php

namespace Modules\Development\DTOs;

use Modules\Development\Http\Requests\StoreIpBlacklistRequest;

readonly class CreateIpBlacklistData
{
    public function __construct(
        public string $ip_address,
    ) {}

    public static function fromRequest(StoreIpBlacklistRequest $request): self
    {
        return new self(...$request->validated());
    }
}
