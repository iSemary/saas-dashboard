<?php

namespace Modules\CRM\DTOs;

use Modules\CRM\Http\Requests\StoreContactRequest;

readonly class CreateContactData
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $mobile = null,
        public ?string $title = null,
        public ?int $company_id = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $postal_code = null,
        public ?string $country = null,
        public ?string $birthday = null,
        public ?string $notes = null,
        public ?string $type = null,
        public ?int $assigned_to = null,
        public ?array $custom_fields = null,
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
