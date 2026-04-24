<?php

namespace Modules\Email\DTOs;

use Modules\Email\Http\Requests\StoreEmailTemplateRequest;

readonly class CreateEmailTemplateData
{
    public function __construct(
        public string $name,
        public string $subject,
        public string $body,
        public ?array $variables,
        public ?string $status,
    ) {}

    public static function fromRequest(StoreEmailTemplateRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'subject' => $this->subject,
            'body' => $this->body,
            'variables' => $this->variables,
            'status' => $this->status ?? 'active',
        ];
    }
}
