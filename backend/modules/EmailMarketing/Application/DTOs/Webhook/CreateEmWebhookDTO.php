<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\DTOs\Webhook;

class CreateEmWebhookDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $url,
        public readonly array $events,
        public readonly ?string $secret = null,
        public readonly bool $is_active = true,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
