<?php

namespace Modules\Payment\DTOs;

class RefundResponse
{
    private bool $success;
    private ?string $refundId = null;
    private ?string $gatewayRefundId = null;
    private string $status;
    private ?float $amount = null;
    private ?float $feeRefunded = null;
    private ?array $gatewayResponse = null;
    private ?string $errorCode = null;
    private ?string $errorMessage = null;
    private ?\DateTime $processedAt = null;
    private ?array $metadata = null;

    public function __construct(bool $success, string $status)
    {
        $this->success = $success;
        $this->status = $status;
        $this->processedAt = new \DateTime();
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

    public function getRefundId(): ?string
    {
        return $this->refundId;
    }

    public function setRefundId(?string $refundId): self
    {
        $this->refundId = $refundId;
        return $this;
    }

    public function getGatewayRefundId(): ?string
    {
        return $this->gatewayRefundId;
    }

    public function setGatewayRefundId(?string $gatewayRefundId): self
    {
        $this->gatewayRefundId = $gatewayRefundId;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getFeeRefunded(): ?float
    {
        return $this->feeRefunded;
    }

    public function setFeeRefunded(?float $feeRefunded): self
    {
        $this->feeRefunded = $feeRefunded;
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

    public function getProcessedAt(): ?\DateTime
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?\DateTime $processedAt): self
    {
        $this->processedAt = $processedAt;
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

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'refund_id' => $this->refundId,
            'gateway_refund_id' => $this->gatewayRefundId,
            'status' => $this->status,
            'amount' => $this->amount,
            'fee_refunded' => $this->feeRefunded,
            'gateway_response' => $this->gatewayResponse,
            'error_code' => $this->errorCode,
            'error_message' => $this->errorMessage,
            'processed_at' => $this->processedAt?->format('Y-m-d H:i:s'),
            'metadata' => $this->metadata,
        ];
    }
}
