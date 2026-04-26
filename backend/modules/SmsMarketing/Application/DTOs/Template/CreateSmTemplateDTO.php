<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\DTOs\Template;

class CreateSmTemplateDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $body = null,
        public readonly ?array $variables = null,
        public readonly string $status = 'draft',
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
