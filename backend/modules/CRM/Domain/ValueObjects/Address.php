<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\ValueObjects;

final readonly class Address
{
    public function __construct(
        private ?string $street = null,
        private ?string $city = null,
        private ?string $state = null,
        private ?string $postalCode = null,
        private ?string $country = null,
        private ?string $countryCode = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            street: $data['street'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            postalCode: $data['postal_code'] ?? null,
            country: $data['country'] ?? null,
            countryCode: $data['country_code'] ?? null
        );
    }

    public function street(): ?string
    {
        return $this->street;
    }

    public function city(): ?string
    {
        return $this->city;
    }

    public function state(): ?string
    {
        return $this->state;
    }

    public function postalCode(): ?string
    {
        return $this->postalCode;
    }

    public function country(): ?string
    {
        return $this->country;
    }

    public function countryCode(): ?string
    {
        return $this->countryCode;
    }

    public function formatted(): string
    {
        $parts = array_filter([
            $this->street,
            $this->city,
            $this->state,
            $this->postalCode,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    public function formattedMultiline(): string
    {
        $lines = [];

        if ($this->street) {
            $lines[] = $this->street;
        }

        $cityStateZip = [];
        if ($this->city) {
            $cityStateZip[] = $this->city;
        }
        if ($this->state) {
            $cityStateZip[] = $this->state;
        }
        if ($this->postalCode) {
            $cityStateZip[] = $this->postalCode;
        }

        if (!empty($cityStateZip)) {
            $lines[] = implode(', ', $cityStateZip);
        }

        if ($this->country) {
            $lines[] = $this->country;
        }

        return implode("\n", $lines);
    }

    public function isEmpty(): bool
    {
        return empty($this->street) &&
               empty($this->city) &&
               empty($this->state) &&
               empty($this->postalCode) &&
               empty($this->country);
    }

    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postalCode,
            'country' => $this->country,
            'country_code' => $this->countryCode,
            'formatted' => $this->formatted(),
        ];
    }

    public function withStreet(string $street): self
    {
        return new self($street, $this->city, $this->state, $this->postalCode, $this->country, $this->countryCode);
    }

    public function withCity(string $city): self
    {
        return new self($this->street, $city, $this->state, $this->postalCode, $this->country, $this->countryCode);
    }

    public function withState(string $state): self
    {
        return new self($this->street, $this->city, $state, $this->postalCode, $this->country, $this->countryCode);
    }

    public function withPostalCode(string $postalCode): self
    {
        return new self($this->street, $this->city, $this->state, $postalCode, $this->country, $this->countryCode);
    }

    public function withCountry(string $country, ?string $countryCode = null): self
    {
        return new self($this->street, $this->city, $this->state, $this->postalCode, $country, $countryCode ?? $this->countryCode);
    }
}
