<?php

namespace Modules\Ticket\DTOs;

use Modules\Ticket\Http\Requests\StoreTicketRequest;

readonly class CreateTicketData
{
    public function __construct(
        public string $subject,
        public string $body,
        public ?string $priority,
        public ?string $status,
        public ?int $category_id,
        public ?int $assigned_to,
    ) {}

    public static function fromRequest(StoreTicketRequest $request): self
    {
        return new self(...$request->validated());
    }
}
