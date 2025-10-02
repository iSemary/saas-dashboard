<?php

namespace Modules\Payment\DTOs;

class WebhookResponse
{
    private bool $success;
    private int $httpStatus;
    private ?string $message = null;
    private ?array $data = null;
    private ?string $eventType = null;
    private ?string $transactionId = null;
    private ?array $actions = null;
    private ?\DateTime $processedAt = null;

    public function __construct(bool $success, int $httpStatus = 200)
    {
        $this->success = $success;
        $this->httpStatus = $httpStatus;
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

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function setHttpStatus(int $httpStatus): self
    {
        $this->httpStatus = $httpStatus;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(?string $eventType): self
    {
        $this->eventType = $eventType;
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

    public function getActions(): ?array
    {
        return $this->actions;
    }

    public function setActions(?array $actions): self
    {
        $this->actions = $actions;
        return $this;
    }

    public function addAction(string $action, array $data = []): self
    {
        if (!$this->actions) {
            $this->actions = [];
        }

        $this->actions[] = [
            'action' => $action,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ];

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

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'http_status' => $this->httpStatus,
            'message' => $this->message,
            'data' => $this->data,
            'event_type' => $this->eventType,
            'transaction_id' => $this->transactionId,
            'actions' => $this->actions,
            'processed_at' => $this->processedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function toHttpResponse(): array
    {
        return [
            'status' => $this->success ? 'success' : 'error',
            'message' => $this->message ?? ($this->success ? 'Webhook processed successfully' : 'Webhook processing failed'),
            'data' => $this->data,
        ];
    }
}
