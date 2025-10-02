<?php

namespace Modules\Payment\DTOs;

class PaymentRequest
{
    private float $amount;
    private string $currency;
    private ?string $customerId = null;
    private ?string $paymentMethodId = null;
    private ?array $paymentMethodData = null;
    private ?string $description = null;
    private ?array $metadata = null;
    private ?array $billingAddress = null;
    private ?array $shippingAddress = null;
    private bool $capture = true;
    private ?string $statementDescriptor = null;
    private ?string $receiptEmail = null;
    private ?string $orderId = null;
    private ?string $invoiceNumber = null;
    private ?string $customerIp = null;
    private ?string $userAgent = null;
    private bool $savePaymentMethod = false;
    private ?string $returnUrl = null;
    private ?string $cancelUrl = null;

    public function __construct(float $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
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

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
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

    public function getPaymentMethodId(): ?string
    {
        return $this->paymentMethodId;
    }

    public function setPaymentMethodId(?string $paymentMethodId): self
    {
        $this->paymentMethodId = $paymentMethodId;
        return $this;
    }

    public function getPaymentMethodData(): ?array
    {
        return $this->paymentMethodData;
    }

    public function setPaymentMethodData(?array $paymentMethodData): self
    {
        $this->paymentMethodData = $paymentMethodData;
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

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getBillingAddress(): ?array
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?array $billingAddress): self
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }

    public function getShippingAddress(): ?array
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?array $shippingAddress): self
    {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }

    public function getCapture(): bool
    {
        return $this->capture;
    }

    public function setCapture(bool $capture): self
    {
        $this->capture = $capture;
        return $this;
    }

    public function getStatementDescriptor(): ?string
    {
        return $this->statementDescriptor;
    }

    public function setStatementDescriptor(?string $statementDescriptor): self
    {
        $this->statementDescriptor = $statementDescriptor;
        return $this;
    }

    public function getReceiptEmail(): ?string
    {
        return $this->receiptEmail;
    }

    public function setReceiptEmail(?string $receiptEmail): self
    {
        $this->receiptEmail = $receiptEmail;
        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(?string $orderId): self
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(?string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    public function getCustomerIp(): ?string
    {
        return $this->customerIp;
    }

    public function setCustomerIp(?string $customerIp): self
    {
        $this->customerIp = $customerIp;
        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getSavePaymentMethod(): bool
    {
        return $this->savePaymentMethod;
    }

    public function setSavePaymentMethod(bool $savePaymentMethod): self
    {
        $this->savePaymentMethod = $savePaymentMethod;
        return $this;
    }

    public function getReturnUrl(): ?string
    {
        return $this->returnUrl;
    }

    public function setReturnUrl(?string $returnUrl): self
    {
        $this->returnUrl = $returnUrl;
        return $this;
    }

    public function getCancelUrl(): ?string
    {
        return $this->cancelUrl;
    }

    public function setCancelUrl(?string $cancelUrl): self
    {
        $this->cancelUrl = $cancelUrl;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'customer_id' => $this->customerId,
            'payment_method_id' => $this->paymentMethodId,
            'payment_method_data' => $this->paymentMethodData,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'billing_address' => $this->billingAddress,
            'shipping_address' => $this->shippingAddress,
            'capture' => $this->capture,
            'statement_descriptor' => $this->statementDescriptor,
            'receipt_email' => $this->receiptEmail,
            'order_id' => $this->orderId,
            'invoice_number' => $this->invoiceNumber,
            'customer_ip' => $this->customerIp,
            'user_agent' => $this->userAgent,
            'save_payment_method' => $this->savePaymentMethod,
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl,
        ];
    }
}
