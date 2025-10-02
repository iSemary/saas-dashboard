<?php

namespace Modules\Payment\DTOs;

class WebhookRequest
{
    private string $payload;
    private array $headers;
    private ?string $signature = null;
    private ?string $eventType = null;
    private ?array $data = null;
    private ?string $gatewayName = null;
    private ?string $eventId = null;
    private ?\DateTime $eventTime = null;

    public function __construct(string $payload, array $headers = [])
    {
        $this->payload = $payload;
        $this->headers = $headers;
        $this->eventTime = new \DateTime();
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function setPayload(string $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? $this->headers[strtolower($name)] ?? null;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(?string $signature): self
    {
        $this->signature = $signature;
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

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getGatewayName(): ?string
    {
        return $this->gatewayName;
    }

    public function setGatewayName(?string $gatewayName): self
    {
        $this->gatewayName = $gatewayName;
        return $this;
    }

    public function getEventId(): ?string
    {
        return $this->eventId;
    }

    public function setEventId(?string $eventId): self
    {
        $this->eventId = $eventId;
        return $this;
    }

    public function getEventTime(): ?\DateTime
    {
        return $this->eventTime;
    }

    public function setEventTime(?\DateTime $eventTime): self
    {
        $this->eventTime = $eventTime;
        return $this;
    }

    public function getParsedPayload(): ?array
    {
        if (!$this->payload) {
            return null;
        }

        $decoded = json_decode($this->payload, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }

    public function toArray(): array
    {
        return [
            'payload' => $this->payload,
            'headers' => $this->headers,
            'signature' => $this->signature,
            'event_type' => $this->eventType,
            'data' => $this->data,
            'gateway_name' => $this->gatewayName,
            'event_id' => $this->eventId,
            'event_time' => $this->eventTime?->format('Y-m-d H:i:s'),
        ];
    }
}
