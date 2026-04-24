<?php

namespace Modules\CRM\DTOs;

use Modules\CRM\Http\Requests\StoreContactRequest;

readonly class CreateContactData
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public ?string $email,
        public ?string $phone,
        public ?string $mobile,
        public ?string $title,
        public ?int $company_id,
        public ?string $address,
        public ?string $city,
        public ?string $state,
        public ?string $postal_code,
        public ?string $country,
        public ?string $birthday,
        public ?string $notes,
        public ?string $type,
        public ?int $assigned_to,
        public ?array $custom_fields,
    ) {}

    public static function fromRequest(StoreContactRequest $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'title' => $this->title,
            'company_id' => $this->company_id,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'birthday' => $this->birthday,
            'notes' => $this->notes,
            'type' => $this->type,
            'assigned_to' => $this->assigned_to,
            'custom_fields' => $this->custom_fields,
        ];
    }
}
