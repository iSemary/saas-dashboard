<?php

namespace Modules\Payment\DTOs;

class RefundRequest
{
    private string $transactionId;
    private float $amount;
    private ?string $reason = null;
    private ?string $reasonDetails = null;
    private ?array $metadata = null;
    private bool $refundFees = false;
    private ?string $refundId = null;

    public function __construct(string $transactionId, float $amount)
    {
        $this->transactionId = $transactionId;
        $this->amount = $amount;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function setTransactionId(string $transactionId): self
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;
        return $this;
    }

    public function getReasonDetails(): ?string
    {
        return $this->reasonDetails;
    }

    public function setReasonDetails(?string $reasonDetails): self
    {
        $this->reasonDetails = $reasonDetails;
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

    public function getRefundFees(): bool
    {
        return $this->refundFees;
    }

    public function setRefundFees(bool $refundFees): self
    {
        $this->refundFees = $refundFees;
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

    public function toArray(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'amount' => $this->amount,
            'reason' => $this->reason,
            'reason_details' => $this->reasonDetails,
            'metadata' => $this->metadata,
            'refund_fees' => $this->refundFees,
            'refund_id' => $this->refundId,
        ];
    }
}
