<?php

namespace Modules\Payment\DTOs;

class PaymentResponse
{
    private bool $success;
    private ?string $transactionId = null;
    private ?string $gatewayTransactionId = null;
    private ?string $gatewayReference = null;
    private string $status;
    private ?float $amount = null;
    private ?string $currency = null;
    private ?array $gatewayResponse = null;
    private ?string $errorCode = null;
    private ?string $errorMessage = null;
    private ?array $fees = null;
    private ?string $authorizationCode = null;
    private ?string $avsResult = null;
    private ?string $cvvResult = null;
    private ?array $fraudCheck = null;
    private ?\DateTime $processedAt = null;
    private ?array $metadata = null;
    private ?string $receiptUrl = null;
    private ?string $redirectUrl = null;

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

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(?string $transactionId): self
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function getGatewayTransactionId(): ?string
    {
        return $this->gatewayTransactionId;
    }

    public function setGatewayTransactionId(?string $gatewayTransactionId): self
    {
        $this->gatewayTransactionId = $gatewayTransactionId;
        return $this;
    }

    public function getGatewayReference(): ?string
    {
        return $this->gatewayReference;
    }

    public function setGatewayReference(?string $gatewayReference): self
    {
        $this->gatewayReference = $gatewayReference;
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

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
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

    public function getFees(): ?array
    {
        return $this->fees;
    }

    public function setFees(?array $fees): self
    {
        $this->fees = $fees;
        return $this;
    }

    public function getAuthorizationCode(): ?string
    {
        return $this->authorizationCode;
    }

    public function setAuthorizationCode(?string $authorizationCode): self
    {
        $this->authorizationCode = $authorizationCode;
        return $this;
    }

    public function getAvsResult(): ?string
    {
        return $this->avsResult;
    }

    public function setAvsResult(?string $avsResult): self
    {
        $this->avsResult = $avsResult;
        return $this;
    }

    public function getCvvResult(): ?string
    {
        return $this->cvvResult;
    }

    public function setCvvResult(?string $cvvResult): self
    {
        $this->cvvResult = $cvvResult;
        return $this;
    }

    public function getFraudCheck(): ?array
    {
        return $this->fraudCheck;
    }

    public function setFraudCheck(?array $fraudCheck): self
    {
        $this->fraudCheck = $fraudCheck;
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

    public function getReceiptUrl(): ?string
    {
        return $this->receiptUrl;
    }

    public function setReceiptUrl(?string $receiptUrl): self
    {
        $this->receiptUrl = $receiptUrl;
        return $this;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(?string $redirectUrl): self
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'transaction_id' => $this->transactionId,
            'gateway_transaction_id' => $this->gatewayTransactionId,
            'gateway_reference' => $this->gatewayReference,
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'gateway_response' => $this->gatewayResponse,
            'error_code' => $this->errorCode,
            'error_message' => $this->errorMessage,
            'fees' => $this->fees,
            'authorization_code' => $this->authorizationCode,
            'avs_result' => $this->avsResult,
            'cvv_result' => $this->cvvResult,
            'fraud_check' => $this->fraudCheck,
            'processed_at' => $this->processedAt?->format('Y-m-d H:i:s'),
            'metadata' => $this->metadata,
            'receipt_url' => $this->receiptUrl,
            'redirect_url' => $this->redirectUrl,
        ];
    }
}
