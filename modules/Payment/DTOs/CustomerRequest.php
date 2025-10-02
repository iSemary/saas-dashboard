<?php

namespace Modules\Payment\DTOs;

class CustomerRequest
{
    private ?string $customerId = null;
    private ?string $email = null;
    private ?string $name = null;
    private ?string $phone = null;
    private ?array $address = null;
    private ?array $metadata = null;
    private ?string $description = null;
    private ?string $taxId = null;
    private ?array $shipping = null;

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(?string $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getAddress(): ?array
    {
        return $this->address;
    }

    public function setAddress(?array $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function setTaxId(?string $taxId): self
    {
        $this->taxId = $taxId;
        return $this;
    }

    public function getShipping(): ?array
    {
        return $this->shipping;
    }

    public function setShipping(?array $shipping): self
    {
        $this->shipping = $shipping;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
            'email' => $this->email,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'metadata' => $this->metadata,
            'description' => $this->description,
            'tax_id' => $this->taxId,
            'shipping' => $this->shipping,
        ];
    }
}
