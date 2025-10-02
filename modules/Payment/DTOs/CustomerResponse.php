<?php

namespace Modules\Payment\DTOs;

class CustomerResponse
{
    private bool $success;
    private ?string $customerId = null;
    private ?string $gatewayCustomerId = null;
    private ?string $email = null;
    private ?string $name = null;
    private ?string $phone = null;
    private ?array $address = null;
    private ?array $metadata = null;
    private ?array $gatewayResponse = null;
    private ?string $errorCode = null;
    private ?string $errorMessage = null;
    private ?\DateTime $createdAt = null;

    public function __construct(bool $success)
    {
        $this->success = $success;
        $this->createdAt = new \DateTime();
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): self
    {
        $this->success = $success;
        return $this;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(?string $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function getGatewayCustomerId(): ?string
    {
        return $this->gatewayCustomerId;
    }

    public function setGatewayCustomerId(?string $gatewayCustomerId): self
    {
        $this->gatewayCustomerId = $gatewayCustomerId;
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

    public function getGatewayResponse(): ?array
    {
        return $this->gatewayResponse;
    }

    public function setGatewayResponse(?array $gatewayResponse): self
    {
        $this->gatewayResponse = $gatewayResponse;
        return $this;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function setErrorCode(?string $errorCode): self
    {
        $this->errorCode = $errorCode;
        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'customer_id' => $this->customerId,
            'gateway_customer_id' => $this->gatewayCustomerId,
            'email' => $this->email,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'metadata' => $this->metadata,
            'gateway_response' => $this->gatewayResponse,
            'error_code' => $this->errorCode,
            'error_message' => $this->errorMessage,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}
