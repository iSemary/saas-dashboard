<?php

namespace Modules\Email\DTOs;

use Modules\Email\Http\Requests\UpdateEmailTemplateRequest;

readonly class UpdateEmailTemplateData
{
    public function __construct(
        public ?string $name = null,
        public ?string $subject = null,
        public ?string $body = null,
        public ?array $variables = null,
        public ?string $status = null,
    ) {}

    public static function fromRequest(UpdateEmailTemplateRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'subject' => $this->subject,
            'body' => $this->body,
            'variables' => $this->variables,
            'status' => $this->status,
        ], fn ($value) => $value !== null);
    }
}
