<?php

namespace Modules\Email\DTOs;

use Modules\Email\Http\Requests\StoreEmailCampaignRequest;

readonly class CreateEmailCampaignData
{
    public function __construct(
        public string $subject,
        public string $body,
        public ?int $template_id,
        public ?string $status,
        public ?string $scheduled_at,
    ) {}

    public static function fromRequest(StoreEmailCampaignRequest $request): self
    {
        return new self(...$request->validated());
    }
}
