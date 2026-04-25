<?php

declare(strict_types=1);

namespace Modules\Survey\Application\DTOs;

class CreateSurveyShareData
{
    public function __construct(
        public int $surveyId,
        public string $channel,
        public ?array $config = null,
        public ?int $maxUses = null,
        public ?string $expiresAt = null,
        public int $createdBy,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            surveyId: $data['survey_id'],
            channel: $data['channel'],
            config: $data['config'] ?? null,
            maxUses: $data['max_uses'] ?? null,
            expiresAt: $data['expires_at'] ?? null,
            createdBy: $data['created_by'],
        );
    }

    public function toArray(): array
    {
        return [
            'survey_id' => $this->surveyId,
            'channel' => $this->channel,
            'config' => $this->config,
            'max_uses' => $this->maxUses,
            'expires_at' => $this->expiresAt,
            'created_by' => $this->createdBy,
        ];
    }
}
