<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\DTOs\Webhook;

class UpdateSmWebhookDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $url = null,
        public readonly ?array $events = null,
        public readonly ?string $secret = null,
        public readonly ?bool $is_active = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
