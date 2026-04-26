<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Application\DTOs;

class CreateCalendarEventData
{
    public function __construct(
        public string $tenantId,
        public string $userId,
        public string $title,
        public string $startsAt,
        public string $endsAt,
        public ?string $description = null,
        public bool $isAllDay = false,
        public ?string $location = null,
        public ?string $recurrenceRule = null,
        public ?array $attendees = null,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            tenantId: $request->input('tenant_id', $request->user()->tenant_id ?? ''),
            userId: $request->input('user_id', $request->user()->id),
            title: $request->input('title'),
            startsAt: $request->input('starts_at'),
            endsAt: $request->input('ends_at'),
            description: $request->input('description'),
            isAllDay: $request->input('is_all_day', false),
            location: $request->input('location'),
            recurrenceRule: $request->input('recurrence_rule'),
            attendees: $request->input('attendees'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'title' => $this->title,
            'description' => $this->description,
            'starts_at' => $this->startsAt,
            'ends_at' => $this->endsAt,
            'is_all_day' => $this->isAllDay,
            'location' => $this->location,
            'recurrence_rule' => $this->recurrenceRule,
            'attendees' => $this->attendees,
        ], fn($v) => $v !== null);
    }
}
