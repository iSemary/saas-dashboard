<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\DTOs\Template;

class UpdateEmTemplateDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $subject = null,
        public readonly ?string $body_html = null,
        public readonly ?string $body_text = null,
        public readonly ?string $thumbnail_url = null,
        public readonly ?string $category = null,
        public readonly ?array $variables = null,
        public readonly ?string $status = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
