<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\DTOs\Campaign;

class CreateEmCampaignDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $subject,
        public readonly ?int $template_id = null,
        public readonly ?int $credential_id = null,
        public readonly ?string $from_name = null,
        public readonly ?string $from_email = null,
        public readonly ?string $body_html = null,
        public readonly ?string $body_text = null,
        public readonly string $status = 'draft',
        public readonly ?string $scheduled_at = null,
        public readonly ?int $ab_test_id = null,
        public readonly ?array $settings = null,
        public readonly ?array $contact_list_ids = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value, $key) => $value !== null && $key !== 'contact_list_ids', ARRAY_FILTER_USE_BOTH);
    }
}
