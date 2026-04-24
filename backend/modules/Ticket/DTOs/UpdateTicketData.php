<?php

namespace Modules\Ticket\DTOs;

use Modules\Ticket\Http\Requests\UpdateTicketRequest;

readonly class UpdateTicketData
{
    public function __construct(
        public ?string $subject = null,
        public ?string $body = null,
        public ?string $priority = null,
        public ?string $status = null,
        public ?int $category_id = null,
        public ?int $assigned_to = null,
    ) {}

    public static function fromRequest(UpdateTicketRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'subject' => $this->subject,
            'body' => $this->body,
            'priority' => $this->priority,
            'status' => $this->status,
            'category_id' => $this->category_id,
            'assigned_to' => $this->assigned_to,
        ], fn ($value) => $value !== null);
    }
}
